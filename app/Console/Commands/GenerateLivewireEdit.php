<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Services\SchemaService;

class GenerateLivewireEdit extends Command
{
    protected $signature = 'generate:livewire-edit {model}';
    protected $description = 'Generate a Livewire edit component from a model';

    public function handle(SchemaService $schemaService)
    {
        $modelName = $this->argument('model');
        $fullModelClass = "App\\Models\\$modelName";

        if (!class_exists($fullModelClass)) {
            $this->error("Model {$modelName} not found!");
            return 1;
        }

        $schema = $schemaService->getTableSchema($fullModelClass);
        $formFields = $this->generateFormFields($schema);

        $namespace = "App\\Livewire\\{$modelName}";
        $code = $this->generateComponentCode($modelName, $namespace, $formFields);

        $directory = app_path("Livewire/" . $modelName);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filePath = "{$directory}/EditResource.php";
        file_put_contents($filePath, $code);

        $this->info("Generated Livewire edit component at: {$filePath}");
        return 0;
    }

    protected function generateFormFields(array $schema): string
    {
        $fields = [];

        foreach ($schema as $columnName => $details) {
            if (in_array($columnName, ['id', '_indexes', '_foreign_keys', 'created_at', 'updated_at', 'deleted_at'])) {
                continue;
            }

            $field = $this->generateFieldByType($columnName, $details);
            if ($field) {
                $fields[] = $field;
            }
        }

        return implode(",\n                ", $fields);
    }

    protected function generateFieldByType(string $columnName, array $details): ?string
    {
        $required = !$details['nullable'] ? '->required()' : '';

        switch ($details['type']) {
            case 'string':
                if (str_contains($columnName, 'email')) {
                    return "TextInput::make('{$columnName}')->email(){$required}";
                }
                if (str_contains($columnName, 'password')) {
                    return "TextInput::make('{$columnName}')
                        ->password()
                        ->dehydrated(fn (\$state): bool => filled(\$state))
                        ->required(fn (string \$operation): bool => \$operation === 'create')";
                }
                if ($details['length'] > 255) {
                    return "MarkdownEditor::make('{$columnName}'){$required}";
                }
                return "TextInput::make('{$columnName}'){$required}";

            case 'text':
                return "MarkdownEditor::make('{$columnName}'){$required}";

            case 'boolean':
                return "Toggle::make('{$columnName}'){$required}";

            case 'integer':
            case 'bigint':
                if (str_ends_with($columnName, '_id')) {
                    $relationName = Str::before($columnName, '_id');
                    $modelName = Str::studly($relationName);
                    return "Select::make('{$columnName}')
                    ->relationship('{$relationName}', 'name')
                    ->searchable()
                    ->preload(){$required}";
                }
                return "TextInput::make('{$columnName}')->numeric(){$required}";

            case 'decimal':
            case 'float':
            case 'double':
                return "TextInput::make('{$columnName}')->numeric()->step('0.01'){$required}";

            case 'date':
                return "DatePicker::make('{$columnName}'){$required}";

            case 'datetime':
                return "DateTimePicker::make('{$columnName}'){$required}";

            default:
                return null;
        }
    }

    protected function generateComponentCode(string $modelName, string $namespace, string $formFields): string
    {
        $modelVariableName = Str::camel($modelName);

        return <<<PHP
<?php

namespace {$namespace};

use App\Models\\{$modelName} as Model;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Filament\Forms\Form;

class EditResource extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array \$data = [];
    public Model \$record;

    public function mount(Model \$record): void
    {
        abort_if(!Gate::allows('update', \$record), 403);

        \$this->record = \$record;
        \$this->form->fill(\$record->toArray());
    }

    public function form(Form \$form): Form
    {
        return \$form
            ->schema([
                {$formFields}
            ])
            ->statePath('data');
    }

    public function save(): void
    {
        abort_if(!Gate::allows('update', \$this->record), 403);

        \$data = \$this->form->getState();

        \$this->record->update(\$data);

        session()->flash('success', '{$modelName} has been updated.');
        \$this->redirectRoute('{$modelVariableName}s.show', \$this->record);
    }

    public function render(): View
    {
        return view('livewire.edit-resource');
    }
}
PHP;
    }
}

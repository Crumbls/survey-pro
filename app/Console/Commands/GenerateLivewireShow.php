<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Services\SchemaService;

class GenerateLivewireShow extends Command
{
    protected $signature = 'generate:livewire-show {model}';
    protected $description = 'Generate a Livewire show component from a model';

    public function handle(SchemaService $schemaService)
    {
        $modelName = $this->argument('model');
        $fullModelClass = "App\\Models\\$modelName";

        if (!class_exists($fullModelClass)) {
            $this->error("Model {$modelName} not found!");
            return 1;
        }

        $schema = $schemaService->getTableSchema($fullModelClass);
        $displayFields = $this->generateDisplayFields($schema);

        $namespace = "App\\Livewire\\{$modelName}";
        $code = $this->generateComponentCode($modelName, $namespace, $displayFields);

        $directory = app_path("Livewire/" . $modelName);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $filePath = "{$directory}/ShowResource.php";
        file_put_contents($filePath, $code);

        $this->info("Generated Livewire show component at: {$filePath}");
        return 0;
    }

    protected function generateDisplayFields(array $schema): string
    {
        $fields = [];

        foreach ($schema as $columnName => $details) {
            if (in_array($columnName, ['_indexes', '_foreign_keys', 'deleted_at', 'password'])) {
                continue;
            }

            $field = $this->generateInfoListByType($columnName, $details);
            if ($field) {
                $fields[] = $field;
            }
        }

        return implode(",\n                ", $fields);
    }

    protected function generateInfoListByType(string $columnName, array $details): ?string
    {
        $label = Str::title(str_replace('_', ' ', $columnName));

        switch ($details['type']) {
            case 'datetime':
            case 'timestamp':
                return "TextEntry::make('{$columnName}')
                    ->label('{$label}')
                    ->dateTime()";

            case 'date':
                return "TextEntry::make('{$columnName}')
                    ->label('{$label}')
                    ->date()";

            case 'boolean':
                return "IconEntry::make('{$columnName}')
                    ->label('{$label}')
                    ->boolean()";

            case 'text':
                return "TextEntry::make('{$columnName}')
                    ->label('{$label}')
                    ->markdown()";

            default:
                if (str_ends_with($columnName, '_id')) {
                    $relationName = Str::before($columnName, '_id');
                    return "TextEntry::make('{$relationName}.name')
                    ->label('{$label}')";
                }
                return "TextEntry::make('{$columnName}')
                    ->label('{$label}')";
        }
    }

    protected function generateComponentCode(string $modelName, string $namespace, string $displayFields): string
    {
        $modelVariableName = Str::camel($modelName);

        return <<<PHP
<?php

namespace {$namespace};

use App\Models\\{$modelName} as Model;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Concerns\InteractsWithInfolists;
use Filament\Infolists\Contracts\HasInfolists;
use Filament\Infolists\Infolist;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class ShowResource extends Component implements HasForms, HasInfolists
{
    use InteractsWithForms;
    use InteractsWithInfolists;

    public Model \$record;

    public function mount(Model \$record): void
    {
        abort_if(!Gate::allows('view', \$record), 403);
        \$this->record = \$record;
    }

    public function infolist(Infolist \$infolist): Infolist
    {
        return \$infolist
            ->record(\$this->record)
            ->schema([
                {$displayFields}
            ]);
    }

    public function render(): View
    {
        return view('livewire.{$modelVariableName}.show-resource');
    }
}
PHP;
    }
}

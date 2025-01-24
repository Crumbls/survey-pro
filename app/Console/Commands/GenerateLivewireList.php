<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;
use App\Services\SchemaService;

class GenerateLivewireList extends Command
{
    protected $signature = 'generate:livewire-list {model}';
    protected $description = 'Generate a Livewire list component from a model';

    public function handle(SchemaService $schemaService)
    {
        $modelName = $this->argument('model');
        $fullModelClass = "App\\Models\\$modelName";

        if (!class_exists($fullModelClass)) {
            $this->error("Model {$modelName} not found!");
            return 1;
        }

        // Get model schema
        $schema = $schemaService->getTableSchema($fullModelClass);

        // Generate columns code
        $columnsCode = $this->generateColumns($schema);

        // Generate component code
        $namespace = "App\\Livewire\\{$modelName}";
        $code = $this->generateComponentCode($modelName, $namespace, $columnsCode);

        // Create directory if it doesn't exist
        $directory = app_path("Livewire/" . $modelName);
        if (!is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        // Save the file
        $filePath = "{$directory}/ListResource.php";
        file_put_contents($filePath, $code);

        $this->info("Generated Livewire list component at: {$filePath}");
        return 0;
    }

    protected function generateColumns(array $schema): string
    {
        $columns = [];

        foreach ($schema as $columnName => $details) {
            // Skip special keys and common columns we might want to exclude
            if (in_array($columnName, ['_indexes', '_foreign_keys', 'deleted_at', 'password'])) {
                continue;
            }

            $column = "TextColumn::make('{$columnName}')";

            // Add label if column name contains underscore
            if (str_contains($columnName, '_')) {
                $label = Str::title(str_replace('_', ' ', $columnName));
                $column .= "\n                ->label('{$label}')";
            }

            // Add date formatting for timestamp columns
            if (in_array($details['type'], ['datetime', 'timestamp'])) {
                $column .= "\n                ->dateTime()";
            }

            $columns[] = $column;
        }

        return implode(",\n            ", $columns);
    }

    protected function generateComponentCode(string $modelName, string $namespace, string $columnsCode): string
    {
        return <<<PHP
<?php

namespace {$namespace};

use App\Livewire\Contracts\HasTenant;
use App\Models\\{$modelName} as Model;
use App\Traits\HasBreadcrumbs;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Filament\Tables\Actions\CreateAction;

class ListResource extends Component implements HasForms, HasTable
{
    use HasBreadcrumbs,
        HasTenant,
        InteractsWithTable,
        InteractsWithForms;

    protected \$isSingle = 1;

    public function mount()
    {
        \$tenantId = request()->tenantId;
        \$this->setTenant(\$tenantId);
        \$tenant = \$this->getTenant();
        \$user = request()->user();

        if (!\$tenant && \$user->tenants()->count() == 1) {
            \$tenant = \$user->tenants()->first();
            return redirect()->route('tenants.{$modelName}.index', \$tenant);
        }
    }

    protected function getTableQuery()
    {
        abort_if(!Gate::allows('viewAny', Model::class), 403);

        \$tenant = \$this->getTenant();
        \$user = request()->user();

        if (\$tenant) {
            \$this->addBreadcrumb('Center: '.\$tenant->name, route('tenants.show', \$tenant));
            \$this->addBreadcrumb('All {$modelName}s');
            \$this->isSingle = true;
        } else {
            \$this->addBreadcrumb('All {$modelName}s');
            \$this->isSingle = false;
        }

        return Model::query()
            ->when(\$tenant, function (\$query) use (\$tenant) {
                return \$query->whereHas('tenants', function (\$query) use (\$tenant) {
                    \$query->where('tenants.id', \$tenant->id);
                });
            })
            ->when(!\$tenant, function (\$query) use (\$user) {
                return \$query->whereHas('tenants', function (\$query) use (\$user) {
                    \$query->whereIn('tenants.id', \$user->tenants->pluck('id'));
                });
            });
    }

    public function table(Table \$table): Table
    {
        return \$table
            ->query(\$this->getTableQuery())
            ->columns([
                {$columnsCode}
            ])
            ->headerActions([
                CreateAction::make('create')
                    ->label('Create New')
                    ->url(function() {
                        \$tenant = \$this->getTenant();
                        return \$tenant ? route('tenants.{$modelName}.create', \$tenant) : route('{$modelName}.create');
                    })
                    ->button()
                    ->color('custom')
                    ->extraAttributes([
                        'class' => 'bg-primary-600 hover:bg-primary-700'
                    ])
                    ->visible(fn() => Gate::allows('create', Model::class))
            ])
            ->actions([
                ActionGroup::make([
                    Action::make('edit')
                        ->label('Edit')
                        ->icon('heroicon-m-pencil-square')
                        ->url(fn (\$record) => route('{$modelName}.edit', \$record))
                        ->color('custom')
                        ->extraAttributes([
                            'class' => 'text-primary-600 hover:text-primary-700'
                        ])
                ])
                ->label('Actions')
                ->icon('heroicon-m-ellipsis-vertical')
                ->size('sm')
                ->color('custom')
                ->extraAttributes([
                    'class' => 'text-primary-600 hover:text-primary-700'
                ])
            ]);
    }

    public function render(): View
    {
        return view('livewire.{$modelName}.list-resource', [
            'breadcrumbs' => \$this->getBreadcrumbs()
        ]);
    }
}
PHP;
    }
}

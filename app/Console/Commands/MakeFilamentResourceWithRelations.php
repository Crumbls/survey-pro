<?php

namespace App\Console\Commands;

use Filament\Commands\MakeRelationManagerCommand;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use ReflectionClass;
use ReflectionMethod;

class MakeFilamentResourceWithRelations extends MakeRelationManagerCommand
{
    protected $signature = 'make:filament-resource-relations {name?} {--soft-deletes} {--view} {--G|generate} {--S|simple} {--F|force}';

    protected $description = 'Creates a Filament resource with automatic relation managers';

    public function handle(): int
    {
        /*
        // First generate the base resource
        $this->call('make:filament-resource', [
            'name' => $this->argument('name'),
            '--soft-deletes' => $this->option('soft-deletes'),
            '--view' => $this->option('view'),
            '--generate' => $this->option('generate'),
            '--simple' => $this->option('simple'),
            '--force' => $this->option('force'),
        ]);
        */

        $model = $this->getModel();

        $resourceName = class_basename($this->argument('name'));
//        dd($f);
        exit;

        $relations = $this->getModelRelations($model);

        foreach ($relations as $relation) {
            $this->generateRelationManager($relation);
        }

        return self::SUCCESS;
    }

    protected function getModel(): string
    {
        $name = $this->argument('name');

        $modelClass = (string) Str::of($name)
            ->studly()
            ->beforeLast('Resource')
            ->trim('/')
            ->trim('\\')
            ->prepend($this->getModelNamespace());

        return $modelClass;
    }

    protected function getModelNamespace(): string
    {
        return 'App\\Models\\';
    }

    protected function getModelRelations(string $modelClass): Collection
    {
        $relations = new Collection();
        $reflection = new ReflectionClass($modelClass);

        foreach ($reflection->getMethods(ReflectionMethod::IS_PUBLIC) as $method) {
            if ($this->isRelationMethod($method)) {
                $relations->push([
                    'name' => $method->getName(),
                    'type' => $this->getRelationType($method),
                    'model' => $this->getRelatedModel($method)
                ]);
            }
        }

        return $relations;
    }

    protected function isRelationMethod(ReflectionMethod $method): bool
    {
        $returnType = $method->getReturnType();

        if (!$returnType) return false;

        $returnClass = $returnType->getName();

        return is_subclass_of($returnClass, 'Illuminate\Database\Eloquent\Relations\Relation');
    }

    protected function getRelationType(ReflectionMethod $method): string
    {
        $returnType = $method->getReturnType()->getName();

        $relations = [
            'Illuminate\Database\Eloquent\Relations\HasOne' => 'HasOne',
            'Illuminate\Database\Eloquent\Relations\HasMany' => 'HasMany',
            'Illuminate\Database\Eloquent\Relations\BelongsTo' => 'BelongsTo',
            'Illuminate\Database\Eloquent\Relations\BelongsToMany' => 'BelongsToMany',
            'Illuminate\Database\Eloquent\Relations\MorphOne' => 'MorphOne',
            'Illuminate\Database\Eloquent\Relations\MorphMany' => 'MorphMany',
            'Illuminate\Database\Eloquent\Relations\MorphTo' => 'MorphTo',
            'Illuminate\Database\Eloquent\Relations\MorphToMany' => 'MorphToMany',
        ];

        foreach ($relations as $class => $type) {
            if (is_subclass_of($returnType, $class) || $returnType === $class) {
                return $type;
            }
        }

        return 'Unknown';
    }

    protected function getRelatedModel(ReflectionMethod $method): string
    {
        $code = file_get_contents($method->getFileName());
        $methodBody = $this->getMethodBody($method, $code);

        preg_match('/return \$this->\w+\(([^)]+)\)/', $methodBody, $matches);
        if (isset($matches[1])) {
            $params = explode(',', $matches[1]);
            $modelClass = trim($params[0]);
            return str_replace(['::class', "'", '"'], '', $modelClass);
        }

        return '';
    }

    protected function getMethodBody(ReflectionMethod $method, string $code): string
    {
        $lines = explode("\n", $code);
        return implode("\n", array_slice($lines, $method->getStartLine() - 1,
            $method->getEndLine() - $method->getStartLine() + 1));
    }

    protected function generateRelationManager(array $relation): void
    {
//        dd($relation);
        if (in_array($relation['type'], ['HasMany', 'BelongsToMany', 'MorphMany', 'MorphToMany'])) {
            $this->info(sprintf('make:filament-relation-manager '.$f));
//            continue;
            $this->call('make:filament-relation-manager', [
                'name' => 'RoleResource',//$this->argument('name'),
                '--relation' => $relation['name'],
            ]);
        }
    }
}

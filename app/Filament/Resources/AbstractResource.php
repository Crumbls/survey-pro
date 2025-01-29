<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ReportResource\Pages;
use App\Filament\Resources\ReportResource\RelationManagers;
use App\Models\Report;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

abstract class AbstractResource extends Resource
{
    public static function getNavigationSort(): int
    {
        return PHP_INT_MAX;
    }

    public static function getNavigationLabel(): string
    {
        $modelName = static::getModelName();
        return __("{$modelName}.plural");
    }

    protected static function getModelName(): string
    {
        $model = static::getModel();
        return Str::snake(Str::pluralStudly(class_basename($model)));
    }
}

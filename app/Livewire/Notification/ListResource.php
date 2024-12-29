<?php

namespace App\Livewire\Notification;

use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\ActionGroup;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Notifications\DatabaseNotification;
use Livewire\Component;
use Filament\Tables\Actions\CreateAction;


class ListResource extends Component implements HasForms, HasTable {
    use InteractsWithTable,
        InteractsWithForms;

    protected function getTableQuery(): Builder
    {
        return DatabaseNotification::query()
            ->where('notifiable_id', auth()->id())
            ->latest();
    }

    protected function getTableColumns(): array
    {
        return [
            IconColumn::make('type')
                ->icon(fn (string $state): string => match ($state) {
                    'App\Notifications\NewUserNotification' => 'heroicon-o-user-plus',
                    'App\Notifications\MessageNotification' => 'heroicon-o-chat-bubble-left-right',
                    'App\Notifications\CommentNotification' => 'heroicon-o-chat-bubble-bottom-center-text',
                    'App\Notifications\ConnectNotification' => 'heroicon-o-link',
                    default => 'heroicon-o-bell',
                })
                ->color(fn (string $state): string => match ($state) {
                    'App\Notifications\NewUserNotification' => 'success',
                    'App\Notifications\MessageNotification' => 'warning',
                    'App\Notifications\CommentNotification' => 'primary',
                    'App\Notifications\ConnectNotification' => 'info',
                    default => 'secondary',
                }),
            TextColumn::make('data.title')
                ->label('Notification')
                ->description(fn ($record) => $record->data['message'] ?? '')
                ->wrap(),
            TextColumn::make('created_at')
                ->label('Time')
                ->dateTime('d M Y \a\t g:i A')
                ->sortable(),
        ];
    }

    protected function getTableActions(): array
    {
        return [
            ActionGroup::make([
                Action::make('mark_as_read')
                    ->icon('heroicon-o-check')
                    ->hidden(fn (DatabaseNotification $record) => $record->read_at !== null)
                    ->action(fn (DatabaseNotification $record) => $record->markAsRead()),
                Action::make('delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->action(fn (DatabaseNotification $record) => $record->delete()),
            ])
        ];
    }

    protected function getTableBulkActions(): array
    {
        return [
            BulkAction::make('mark_as_read')
                ->icon('heroicon-o-check')
                ->action(fn (Collection $records) => $records->each->markAsRead()),
            BulkAction::make('delete')
                ->icon('heroicon-o-trash')
                ->color('danger')
                ->action(fn (Collection $records) => $records->each->delete()),
        ];
    }

    protected function getTableFilters(): array
    {
        return [
            SelectFilter::make('type')
                ->options([
                    'App\Notifications\NewUserNotification' => 'New User',
                    'App\Notifications\MessageNotification' => 'Message',
                    'App\Notifications\CommentNotification' => 'Comment',
                    'App\Notifications\ConnectNotification' => 'Connect',
                ]),
            Filter::make('unread')
                ->query(fn (Builder $query) => $query->whereNull('read_at')),
        ];
    }
    protected function getTableEmptyStateHeading(): string
    {
        return 'Your Inbox is Empty';
    }

// If you want to remove the description text that appears below the heading:
    protected function getTableEmptyStateDescription(): ?string
    {
        return null;
    }
    public function render(): View
    {
        return view('livewire.notification.list-resource');
    }
}

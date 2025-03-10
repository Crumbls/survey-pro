<?php

namespace App\Livewire\User;

use App\Livewire\Contracts\HasTenant;
use App\Models\Client;
use App\Models\Report;
use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use App\Traits\HasBreadcrumbs;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Illuminate\Support\Str;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Actions;
use Silber\Bouncer\BouncerFacade;

class CreateResource extends Component implements HasForms
{
    use HasBreadcrumbs,
        InteractsWithForms;

    public ?array $data = [];
    public bool $emailExists = false;
    public bool $showPassword = false;
    public bool $isFormValid = false;

    public ?Client $client = null;
    public ?Tenant $tenant = null;

    public function mount() {
        abort_if(!Gate::allows('create', Report::class), 403);

        $user = request()->user();

        /**
         * List client users instead?
         */


        if (!$this->tenant) {
            if ($user->tenants()->count() == 1) {
                $tenant = $user->tenants()->first();
                if (!Gate::allows('viewAny', \App\Models\Report::class)) {
                    return redirect()->route('tenants.reports.show', $this->tenant);
                }

            }
        }

        if ($this->tenant) {
            $this->addBreadcrumb('Center: '.$this->tenant->name, route('tenants.show', $this->tenant));
        } else {
            $this->addBreadcrumb('All Centers', route('tenants.index'));
        }

        $this->addBreadcrumb('Create User');

        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Select::make('tenant_id')
                        ->label(trans('tenants.singular'))
                        ->options(function() {
                            return Auth::user()->tenants()->orderBy('tenants.name','asc')->pluck('tenants.name', 'tenants.id');
                        })
                        ->hidden(function() {
                            return $this->tenant;
                        })
                        ,
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->rules([
                                fn () => function (string $attribute, mixed $value, \Closure $fail) {
                                    if ($value === auth()->user()->email) {
                                        $fail('You cannot use your own email address.');
                                    }
                                },
                                fn () => function (string $attribute, mixed $value, \Closure $fail) {
                                    if ($this->tenant) {
                                        if ($this->tenant->users()->where('email', $value)->first()) {
                                            $fail('This user is already part of this center');
                                        }
                                    }
                                }
                            ])
                            ->live(onBlur: true)
                            ->helperText(function ($state, TextInput $component) {
                                if ($state && $temp = $component->getValidationRules()) {
                                    if (Validator::make(['email' => $state], $temp)->fails()) {
                                        return null;
                                    }
                                }
                                return $state && $this->emailExists
                                    ? 'This user is already in the system and will be invited to join this center.'
                                    : null;
                            })
                            ->afterStateUpdated(function (Get $get, Set $set, ?string $state) {
                                if (!$state) {
                                    $this->emailExists = false;
                                    $this->showPassword = false;
                                    $this->validateFormState();
                                    return;
                                }

                                if (!filter_var($state, FILTER_VALIDATE_EMAIL)) {
                                    $this->emailExists = false;
                                    $this->showPassword = false;
                                    $this->validateFormState();
                                    return;
                                }

                                $emailExists = User::where('email', $state)->exists();
                                $this->emailExists = $emailExists;
                                $this->showPassword = !$emailExists;

                                if ($emailExists) {
                                    $set('password', null);
                                    $set('password_confirmation', null);
                                    $set('name', null);
                                }

                                $this->validateFormState();
                            }),

                        TextInput::make('name')
                            ->label('Full Name')
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->required(fn () => $this->showPassword)
                            ->visible(fn () => $this->showPassword)
                            ->afterStateUpdated(fn () => $this->validateFormState()),

                        Grid::make(1)
                            ->schema([
                                TextInput::make('password')
                                    ->password()
                                    ->required(fn () => $this->showPassword)
                                    ->visible(fn () => $this->showPassword)
                                    ->minLength(8)
                                    ->rules(['contains_number', 'contains_special_character'])
                                    ->live(onBlur: true)
                                    ->suffixAction(
                                        Action::make('generatePassword')
                                            ->icon('heroicon-m-arrow-path')
                                            ->action(function (Set $set) {
                                                $password = Str::password(12, true, true, true, true);
                                                $set('password', $password);
                                                $set('password_confirmation', $password);
                                                $this->validateFormState();
                                            })
                                    )
                                    ->afterStateUpdated(fn () => $this->validateFormState())
                                    ->helperText(function (?string $state): string {
                                        if (!$state) {
                                            return 'Password strength: none';
                                        }

                                        $strength = $this->getPasswordStrength($state);
                                        return "Password strength: {$strength}";
                                    }),

                                TextInput::make('password_confirmation')
                                    ->password()
                                    ->required(fn () => $this->showPassword)
                                    ->visible(fn () => $this->showPassword)
                                    ->same('password')
                                    ->minLength(8)
                                    ->live(onBlur: true)
                                    ->afterStateUpdated(fn () => $this->validateFormState()),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    private function validateFormState(): void
    {
        $data = $this->form->getState();

        // Email is always required
        $isEmailValid = !empty($data['email']) && filter_var($data['email'], FILTER_VALIDATE_EMAIL);

        // If email exists, we only need valid email
        if ($this->emailExists) {
            $this->isFormValid = $isEmailValid;
            return;
        }

        // If email doesn't exist, validate all required fields
        if ($this->showPassword) {
            $this->isFormValid = $isEmailValid &&
                !empty($data['name']) &&
                !empty($data['password']) &&
                strlen($data['password']) >= 8 &&
                $data['password'] === $data['password_confirmation'] &&
                preg_match('/[0-9]/', $data['password']) &&
                preg_match('/[^A-Za-z0-9]/', $data['password']);
            return;
        }

        $this->isFormValid = false;
    }

    private function getPasswordStrength(string $password): string
    {
        $length = strlen($password);
        $hasNumber = preg_match('/[0-9]/', $password);
        $hasSpecial = preg_match('/[^A-Za-z0-9]/', $password);
        $hasUpper = preg_match('/[A-Z]/', $password);
        $hasLower = preg_match('/[a-z]/', $password);

        $score = 0;
        if ($length >= 12) $score++;
        if ($hasNumber) $score++;
        if ($hasSpecial) $score++;
        if ($hasUpper && $hasLower) $score++;

        return match($score) {
            0 => 'weak',
            1 => 'moderate',
            2 => 'strong',
            3, 4 => 'very strong',
            default => 'weak'
        };
    }

    public function create()
    {
        if (!$this->isFormValid) {
            return;
        }

        $data = $this->form->getState();

        $record = User::where('email', $data['email'])->first();

        $newRecord = !(bool)$record;

        if (!$record) {
            $data['password'] = Hash::make($data['password']);
            $record = User::create($data);

            Notification::make()
                ->title('users.created')
                ->success()
                ->send();


        }

        $roleTenant = null;

        if (
            !$this->tenant
            &&
            array_key_exists('tenant_id', $this->data)
            &&
            $this->data['tenant_id']
        ) {
            $this->tenant = Tenant::findOrFail($this->data['tenant_id']);
        }

        if ($this->tenant) {
            $roleTenant = $this->tenant
                ->roles()
                ->firstOrCreate([
                    'title' => 'Center Member'
                ]);
        } else {
            $roleTenant = Role::firstOrCreate([
                'title' => 'Center Member',
                'tenant_id' => null
            ]);
        }

        if ($this->client) {
            abort(500);
        } else if ($this->tenant) {
//            dd($this->tenant->users()->where('users.id', $record->getKey())->get(), $record->getKey());
            if (!$this->tenant->users()->where('users.id', $record->getKey())->exists()) {
                $this->tenant->users()->attach($record, [
                    'role_id' => $roleTenant?->getKey()
                ]);

                Notification::make()
                    ->title('users.tenant_attached')
                    ->success()
                    ->send();
            }
            return redirect()->route('tenants.users.index', $this->tenant);
        } else {
            abort(500);
        }

    }

    public function render(): View
    {
        return view('livewire.user.create-resource', [
            'breadcrumbs' => $this->getBreadcrumbs()
        ]);
    }
}

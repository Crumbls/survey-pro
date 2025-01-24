<?php

namespace App\Livewire\User;

use App\Livewire\Contracts\HasTenant;
use App\Models\Report;
use App\Models\Role;
use App\Models\User;
use App\Traits\HasBreadcrumbs;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Illuminate\Support\Str;
use Filament\Forms\Components\Actions\Action;
use Filament\Forms\Components\Actions;

class CreateResource extends Component implements HasForms
{
    use HasTenant,
        HasBreadcrumbs, InteractsWithForms;

    public ?array $data = [];
    public bool $emailExists = false;
    public bool $showPassword = false;
    public bool $isFormValid = false;

    public function mount() {
        abort_if(!Gate::allows('create', Report::class), 403);

        $tenantId = request()->tenantId;

        $this->setTenant($tenantId);

        $tenant = $this->getTenant();

        $user = request()->user();

        if (!$tenant) {
            if ($user->tenants()->count() == 1) {
                $tenant = $user->tenants()->first();
                if (!Gate::allows('viewAny', \App\Models\Report::class)) {
                    return redirect()->route('tenants.reports.show', $tenant);
                }

            }
        }

        if ($tenant) {
            $this->addBreadcrumb('Center: '.$tenant->name, route('tenants.show', $tenant));
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
                        TextInput::make('email')
                            ->email()
                            ->required()
                            ->live(onBlur: true)
                            ->helperText(fn () => $this->emailExists
                                ? 'This user is already in the system and will be invited to join this center.'
                                : null)
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

        $tenant = $this->getTenant();

        $data = $this->form->getState();

        $record = User::where('email', $data['email'])->first();


        if (!$record) {
            $data['password'] = Hash::make($data['password']);
            $record = User::create($data);
        }

        if ($tenant) {
            if ($record->tenants()->where('tenants.id', $tenant->getKey())->count()) {
                /**
                 * Already a member of this tenant.
                 */
                session()->flash('success', 'Already a member');

            } else {

                /**
                 * TODO: Send invite to user to join center if they are not already a member.
                 * For now, we just add them and show a notification.
                 */
                $role = Role::firstOrCreate(['name' => 'Center Member']);

                $tenant->users()->attach($record->getKey(), [
                    'role_id' => $role->id,
                ]);
            }
        } else {
            dd(__LINE__);
        }


        if ($record->wasRecentlyCreated) {
            session()->flash('success', 'Test 1');
        } else {
            session()->flash('success', 'Test 2');

        }

        return $this->redirect(route('users.index'));


        return;

dd($data);
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $this->showPassword ? bcrypt($data['password']) : null,
        ]);

    }

    public function render(): View
    {
        return view('livewire.user.create-resource', [
            'breadcrumbs' => $this->getBreadcrumbs()
        ]);
    }
}

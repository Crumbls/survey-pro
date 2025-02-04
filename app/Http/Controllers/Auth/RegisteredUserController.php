<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use App\Services\ClientService;
use App\Services\TenantService;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
            ]);

        $tenant = app(TenantService::class)->getOrCreateDefault($user);

        event(new Registered($user));

        Auth::login($user);

        $service = app(\App\Services\TenantService::class);

        $service->createDefaultRolesPermissions($tenant);

        $role = \Silber\Bouncer\Database\Role::withoutGlobalScopes()->firstOrCreate([
            'name' => 'tenant-owner',
            'scope' => $tenant->getKey()
        ], [
            'title' => 'Center Owner',
        ]);

        \Silber\Bouncer\BouncerFacade::scope()->to($tenant->getKey());

        if (!$user->roles()->where('roles.id',$role->getKey())->exists()) {
            $role->assignTo($user);
        }


        return redirect(RouteServiceProvider::HOME);
    }
}

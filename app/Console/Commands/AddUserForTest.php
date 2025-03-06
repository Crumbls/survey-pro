<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;

class AddUserForTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:auft';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scan PHP files for missing declare(strict_types=1); declarations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $tenant = Tenant::where('uuid', 'ff591b65-eee5-4d20-ab92-ffd1c6fe18da')->take(1)->firstOrFail();


        $uta = User::whereNotIn('users.id', $tenant->users()->select('users.id'))->inRandomOrder()->take(1)->first();

        if (!$uta) {
            return;
        }

        $role = $tenant->roles()->where('tilte','<>','Center Owner')->inRandomOrder()->take(1)->first();

        if (!$role) {
            return;
        }

        $tenant->users()->attach($uta, [
            'role_id' => $role->getKey()
        ]);
    }
}

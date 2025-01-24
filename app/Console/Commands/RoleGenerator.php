<?php

namespace App\Console\Commands;

use App\Models\Ability;
use App\Models\Role;
use App\Models\User;
use App\Providers\TestPipeline;
use App\Services\SchemaService;
use Illuminate\Console\Command;

class RoleGenerator extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'roles:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $role = Role::firstOrCreate(
            ['name' => 'administrator'],
            ['title' => 'Administrator', 'level' => 100]
        );

        $abilities = Ability::all();
        $role->abilities()->sync($abilities->pluck('id'));

        $this->info("Administrator role created with {$abilities->count()} abilities");

    }
}

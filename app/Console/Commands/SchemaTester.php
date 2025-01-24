<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Providers\TestPipeline;
use App\Services\SchemaService;
use Illuminate\Console\Command;

class SchemaTester extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:schema-test';

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
        dd(app(SchemaService::class)->getTableSchema(User::class));
        $schemaService = new SchemaService();
        $schema = $schemaService->getTableSchema(User::class);
    }
}

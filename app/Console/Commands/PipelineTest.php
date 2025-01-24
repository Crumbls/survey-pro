<?php

namespace App\Console\Commands;

use App\Providers\TestPipeline;
use Illuminate\Console\Command;

class PipelineTest extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:pipeline-test';

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
        $data = [
            'a' => 'b'
        ];


        $result = app('stateful-pipeline')
            ->send($data)
            ->through([
                TestPipeline::class
//                ProcessDataPipe::class,
  //              ValidateResultsPipe::class,
    //            SaveResultsPipe::class,
            ])
            ->thenReturn();
        //
    }
}

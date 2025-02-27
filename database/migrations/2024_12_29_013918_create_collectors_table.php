<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;


return new class extends Migration
{
    public function up()
    {
        Schema::create('collectors', function (Blueprint $table) {
            $table->id();
            $table
                ->foreignId('client_id')
                ->nullable()
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('survey_id')->constrained()->onDelete('cascade');
            $table->string('name');
            $table->string('type'); // url, manual, api
            $table->string('status')->default('open'); // active, paused, closed
            $table->json('configuration');
            $table->string('unique_code')->nullable();
            $table->unsignedBigInteger('goal')
                ->nullable()
                ->default(null);
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['survey_id', 'unique_code']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('collectors');
    }
};

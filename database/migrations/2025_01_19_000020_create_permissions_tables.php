<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
    //    Schema::dropIfExists('role_abilities');
//        Schema::dropIfExists('permissions');
//        Schema::dropIfExists('abilities');
        //Schema::dropIfExists('roles');
        if (!Schema::hasTable('permissions')) {


            Schema::create('permissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('ability_id')->constrained()->cascadeOnDelete();
                $table->string('entity_type')->nullable();
                $table->bigInteger('entity_id')->nullable();
                $table->boolean('forbidden')->default(false);
                $table->timestamps();
            });
        }


    }

    public function down()
    {
        Schema::dropIfExists('permissions');
    }
};

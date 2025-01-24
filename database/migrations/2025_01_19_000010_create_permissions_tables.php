<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        return;
    //    Schema::dropIfExists('role_abilities');
//        Schema::dropIfExists('permissions');
//        Schema::dropIfExists('abilities');
        //Schema::dropIfExists('roles');
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('title')->nullable();
                $table->integer('level')->default(0);
                $table->timestamps();
            });
        }


        Schema::create('abilities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('title')->nullable();
            $table->string('entity_type')->nullable();
            $table->bigInteger('entity_id')->nullable();
            $table->boolean('only_owned')->default(false);
            $table->timestamps();

            $table->unique(['name', 'entity_type', 'entity_id']);
        });

        Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ability_id')->constrained()->cascadeOnDelete();
            $table->string('entity_type')->nullable();
            $table->bigInteger('entity_id')->nullable();
            $table->boolean('forbidden')->default(false);
            $table->timestamps();
        });

        Schema::create('role_abilities', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('ability_id')->constrained()->cascadeOnDelete();
            $table->primary(['role_id', 'ability_id']);
        });

    }

    public function down()
    {
//        Schema::dropIfExists('roles');
        return;
//        Schema::dropIfExists('tenant_user_role');
        Schema::dropIfExists('role_abilities');
        Schema::dropIfExists('permissions');
        Schema::dropIfExists('abilities');
    }
};

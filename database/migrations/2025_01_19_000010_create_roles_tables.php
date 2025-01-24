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
        Schema::dropIfExists('roles');
        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('title')->nullable();
                $table->integer('level')->default(0);
                $table->timestamps();
            });
        }

    }

    public function down()
    {
        Schema::dropIfExists('roles');
    }
};

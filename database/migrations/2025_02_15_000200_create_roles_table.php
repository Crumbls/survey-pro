<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Silber\Bouncer\Database\Models;

return new class extends Migration
{
    public function getTable() : string {
        return 'roles';
    }

    public function up()
    {
        if (Schema::hasTable($this->getTable())) {
            return;
        }

        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Tenant::class)->nullable();
            $table->string('name');
            $table->string('title')->nullable();
            $table->timestamps();

            $table->unique(
                ['name', 'scope'],
                'roles_name_unique'
            );
        });
    }

    public function down()
    {
        Schema::dropIfExists($this->getTable());
    }
};

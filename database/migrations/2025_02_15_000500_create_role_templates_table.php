<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Silber\Bouncer\Database\Models;

return new class extends Migration
{
    public function getTable() : string {
        return with(new \App\Models\RoleTemplate())->getTable();
    }
    public function up()
    {
        if (Schema::hasTable($this->getTable())) {
            return;
        }

        Schema::create($this->getTable(), function (Blueprint $table) {
                $table->id();
                $table->string('name')->unique();
                $table->string('display_name');
                $table->text('description')->nullable();
                $table->boolean('is_global')->default(false);
                $table->json('default_permissions');
                $table->boolean('is_active')->default(true);
                $table->timestamps();
                $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::dropIfExists($this->getTable());
    }
};

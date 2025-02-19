<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Silber\Bouncer\Database\Models;

return new class extends Migration
{
    public function getTable() : string {
        return 'permissions';
    }
    public function up()
    {
        if (Schema::hasTable($this->getTable())) {
            return;
        }

        Schema::create($this->getTable(), function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\Ability::class)->index();
            $table->foreignIdFor(\App\Models\Role::class)->index();
            $table->boolean('forbidden')->default(false);

            $table->index(
                ['ability_id', 'role_id'],
                'permissions_entity_index'
            );

            $table->foreign('ability_id')
                ->references('id')->on('abilities')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists($this->getTable());
    }
};

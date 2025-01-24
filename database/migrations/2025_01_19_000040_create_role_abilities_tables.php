<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('role_abilities')) {
            Schema::create('role_abilities', function (Blueprint $table) {
                $table->foreignId('role_id')->constrained()->cascadeOnDelete();
                $table->foreignId('ability_id')->constrained()->cascadeOnDelete();
                $table->primary(['role_id', 'ability_id']);
            });
        }


    }

    public function down()
    {
        Schema::dropIfExists('role_abilities');
    }
};

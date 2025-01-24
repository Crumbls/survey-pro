<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('abilities')) {
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
        }



    }

    public function down()
    {
        Schema::dropIfExists('abilities');
    }
};

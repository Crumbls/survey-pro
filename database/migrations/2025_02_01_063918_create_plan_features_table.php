<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('plan_features', function (Blueprint $table) {
            // Columns
            $table->id();
            $table->unsignedBigInteger('plan_id');
            $table->string('uuid')->unique();
            $table->string('name');
            $table->string('description')->nullable();
            $table->string('value');
            $table->unsignedSmallInteger('resettable_period')->default(0);
            $table->string('resettable_interval')->default('month');
            $table->unsignedMediumInteger('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->unique(['plan_id', 'slug']);
            $table->foreign('plan_id')->references('id')->on('plans')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_features');
    }
};

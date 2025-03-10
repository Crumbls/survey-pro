<?php


use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        if (Schema::hasTable('tenant_user_role')) {
            return;
        }
        Schema::create('tenant_user_role', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')
                ->constrained()
                     ->onDelete('cascade')
                ->nullable()
                ->default(null);
            $table->foreignId('user_id')
                ->constrained()->onDelete('cascade');
            $table->foreignId('role_id')
                ->nullable()
                ->default(null)
                ->constrained()
                ->onDelete('cascade');
            $table->timestamps();
            // Ensure a user can only have one role per tenant
            $table->unique(['tenant_id', 'user_id']);
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
    }
};

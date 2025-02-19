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
        if (Schema::hasTable('plan_subscription_usage')) {
            return;
        }
        return;
        Schema::create('plan_subscription_usages', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('subscription_id');
            $table->unsignedBigInteger('feature_id');
            $table->unsignedSmallInteger('used');
            $table->dateTime('valid_until')->nullable();
            $table->string('timezone')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['subscription_id', 'feature_id']);
            $table->foreign('subscription_id')->references('id')->on('plan_subscriptions')
                ->onDelete('cascade')->onUpdate('cascade');
            $table->foreign('feature_id')->references('id')->on('plan_features')
                ->onDelete('cascade')->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {

        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('plan_subscription_usage');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the ticket_statuses table.
 *
 * This migration sets up the database schema for ticket status levels,
 * including attributes like title, color, and display order.
 *
 * @package Padmission\Ticket\Database\Migrations
 */
return new class extends \Padmission\Ticket\Database\Migrations\AbstractMigration
{
    /**
     * Run the migrations.
     *
     * Creates the ticket_statuses table with the following columns:
     * - id: Primary key
     * - title: Status level name
     * - color_foreground: Foreground color (text color)
     * - color_background: Background color
     * - ord: Display order
     * - timestamps: created_at and updated_at
     */
    public function up(): void
    {
        Schema::create('ticket_statuses', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Status title
            $table->string('title');

            // Color settings
            $table->string('color_foreground')->default('#000000')
                ->comment('Text color for the status');

            $table->string('color_background')->default('#FFFFFF')
                ->comment('Background color for the status');

            // Display order
            $table->unsignedInteger('ord')->default(0)
                ->comment('Ordering priority for display');

            // Timestamp columns
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the ticket_statuses table after disabling foreign key constraints
     * to ensure safe removal of the table.
     */
    public function down(): void
    {
        // Disable foreign key checks to prevent constraint violations
        Schema::disableForeignKeyConstraints();

        // Remove the ticket_statuses table
        Schema::dropIfExists('ticket_statuses');
    }
};

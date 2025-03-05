<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the ticket_comments table.
 *
 * This migration sets up the database schema for ticket comments,
 * establishing relationships between comments, users, and tickets.
 *
 * @package Padmission\Ticket\Database\Migrations
 */
return new class extends \Padmission\Ticket\Database\Migrations\AbstractMigration
{
    /**
     * Run the migrations.
     *
     * Creates the ticket_comments table with the following columns:
     * - id: Primary key
     * - user_id: Foreign key to users table (comment author)
     * - ticket_id: Foreign key to tickets table
     * - description: Comment text (optional)
     * - timestamps: created_at and updated_at
     */
    public function up(): void
    {
        Schema::create('ticket_comments', function (Blueprint $table) {
            /** @var \Padmission\Ticket\Services\ModelResolver $service */
            $service = $this->getService();

            // Primary key
            $table->id();

            // Foreign key to users table
            $table->foreignIdFor($service->resolve('user'))
                ->comment('User who created the comment');

            // Foreign key to tickets table
            $table->foreignIdFor($service->resolve('ticket'))
                ->comment('Ticket associated with the comment');

            // Comment content
            $table->longText('description')->nullable()
                ->comment('Text of the comment');

            // Timestamp columns
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the ticket_comments table after disabling foreign key constraints
     * to ensure safe removal of the table.
     */
    public function down(): void
    {
        // Disable foreign key checks to prevent constraint violations
        Schema::disableForeignKeyConstraints();

        // Remove the ticket_comments table
        Schema::dropIfExists('ticket_comments');
    }
};

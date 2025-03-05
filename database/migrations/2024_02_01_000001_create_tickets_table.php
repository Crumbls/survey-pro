<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration for creating the tickets table.
 *
 * This migration sets up the database schema for tickets,
 * including relationships with status, priority, users, and other metadata.
 *
 * @package Padmission\Ticket\Database\Migrations
 */
return new class extends \Padmission\Ticket\Database\Migrations\AbstractMigration
{

    /**
     * Run the migrations.
     *
     * Creates the tickets table with the following columns:
     * - id: Primary key
     * - title: Ticket title
     * - description: Detailed ticket description (optional)
     * - status_id: Foreign key to ticket status (optional)
     * - priority_id: Foreign key to ticket priority (optional)
     * - submitter_id: Foreign key to user who submitted the ticket
     * - submitter_data: Additional submitter information in JSON format
     * - assigned_agent_id: Foreign key to assigned agent (optional)
     * - timestamps: created_at and updated_at
     * - soft deletes: Allows for soft deletion of tickets
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            /** @var \Padmission\Ticket\Services\ModelResolver $service */
            $service = $this->getService();

            // Primary key
            $table->id();

            // Ticket details
            $table->string('title');
            $table->longText('description')->nullable();

            // Relationships
            $table->foreignIdFor($service->resolve('status'))
                ->nullable()
                ->comment('Foreign key to ticket status');

            $table->foreignIdFor($service->resolve('priority'))
                ->nullable()
                ->comment('Foreign key to ticket priority');

            $table->foreignIdFor($service->resolve('user'), 'submitter_id')
                ->comment('Foreign key to ticket submitter');

            $table->json('submitter_data')->nullable()
                ->comment('Additional JSON data about the submitter');

            $table->foreignIdFor($service->resolve('user'), 'assigned_agent_id')
                ->nullable()
                ->comment('Foreign key to assigned support agent');

            // Timestamps and soft deletes
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the tickets table after disabling foreign key constraints
     * to ensure safe removal of the table.
     */
    public function down(): void
    {
        // Disable foreign key checks to prevent constraint violations
        Schema::disableForeignKeyConstraints();

        // Remove the tickets table
        Schema::dropIfExists('tickets');
    }
};

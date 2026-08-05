<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');
            $table->foreignId('conference_id')
                ->constrained()
                ->onDelete('cascade');
            $table->string('ticket_reference');
            $table->string('ticket_source')->nullable();
            $table->json('ticket_metadata')->nullable();
            $table->enum('approval_status', [
                'pending',
                'approved',
                'rejected'
            ])->default('pending');
            $table->foreignId('approved_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->unique(['user_id', 'conference_id']);
            $table->text('approval_remarks')->nullable();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('registrations');
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {

            $table->id();

            $table->foreignId('conference_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('ticket_reference')->unique();

            $table->string('ticket_holder_name');

            $table->string('ticket_holder_email');

            $table->string('ticket_source')->nullable();

            $table->enum('status', [
                'unused',
                'used',
                'cancelled'
            ])->default('unused');

            $table->foreignId('imported_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('used_by_user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamp('used_at')->nullable();

            $table->timestamps();

            $table->index([
                'conference_id',
                'ticket_holder_email'
            ]);

            $table->index([
                'conference_id',
                'status'
            ]);

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};

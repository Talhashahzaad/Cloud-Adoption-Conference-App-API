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
        Schema::create('conferences', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->date('conference_date');
            $table->string('venue_name');
            $table->text('venue_address');
            $table->string('venue_city');
            $table->string('venue_state');
            $table->date('active_start_date');
            $table->date('active_end_date');
            $table->string('google_map_url');
            $table->enum('status', [
                'active',
                'inactive'
            ])->default('active');
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conferences');
    }
};
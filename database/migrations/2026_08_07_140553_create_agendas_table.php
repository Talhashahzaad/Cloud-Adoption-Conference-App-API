<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('agendas', function (Blueprint $table) {

            $table->id();

            $table->foreignId('conference_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('session_name');

            $table->string('presenter');

            $table->date('session_date');

            $table->string('duration');

            $table->string('place');

            $table->string('room');

            $table->timestamps();

            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('agendas');
    }
};

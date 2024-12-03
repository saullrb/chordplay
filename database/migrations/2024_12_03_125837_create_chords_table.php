<?php

use App\Models\Chord;
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
        Schema::create('chords', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignIdFor(Chord::class, 'simplified_chord_id')->nullable()->constrained()->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chords');
    }
};

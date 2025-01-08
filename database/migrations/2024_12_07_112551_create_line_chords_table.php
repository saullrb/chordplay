<?php

use App\Models\Chord;
use App\Models\SongLine;
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
        Schema::create('line_chords', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(SongLine::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Chord::class)->constrained();
            $table->integer('position');
            $table->timestamps();

            $table->unique(['song_line_id', 'position']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chord_placements');
    }
};

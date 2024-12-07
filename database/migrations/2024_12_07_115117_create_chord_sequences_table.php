<?php

use App\Models\Chord;
use App\Models\SongSection;
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
        Schema::create('chord_sequences', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(SongSection::class)->constrained()->cascadeOnDelete();
            $table->foreignIdFor(Chord::class)->constrained();
            $table->integer('order');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chord_sequences');
    }
};

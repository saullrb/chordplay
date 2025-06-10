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
        Schema::create('chords', function (Blueprint $table): void {
            $table->id();
            $table->string('name')->unique();
            $table->enum('variation', Chord::VARIATIONS);
            $table->timestamps();
        });

        foreach (Chord::BASE_NOTES as $base_note) {
            foreach (Chord::VARIATIONS as $variation) {
                $chord = $base_note.$variation;
                Chord::create([
                    'name' => $chord,
                    'variation' => $variation,
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chords');
    }
};

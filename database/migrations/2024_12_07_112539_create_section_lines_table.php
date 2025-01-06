<?php

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
        Schema::create('section_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(SongSection::class);
            $table->integer('sequence')->default(1);
            $table->string('lyrics');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lyric_lines');
    }
};

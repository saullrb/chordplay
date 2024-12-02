<?php

use App\Enums\SongSectionEnum;
use App\Models\Song;
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
        Schema::create('song_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Song::class)->constrained();
            $table->boolean('is_lyrical')->default(true);
            $table->integer('order')->default(1);
            $table->text('content');
            $table->enum('type', array_column(SongSectionEnum::cases(), 'value'));
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('song_sections');
    }
};

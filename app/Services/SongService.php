<?php

namespace App\Services;

use App\Enums\SongKeyEnum;
use App\Models\Song;
use App\Rules\ValidContent;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class SongService
{
    public function store(array $data, int $artist_id): Song
    {
        $content_rule = new ValidContent;
        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'key' => ['required', new \Illuminate\Validation\Rules\Enum(SongKeyEnum::class)],
            'content' => ['required', 'string', $content_rule],
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        return DB::transaction(function () use ($data, $artist_id, $content_rule) {
            $song = Song::create([
                'name' => $data['name'],
                'artist_id' => $artist_id,
                'key' => $data['key'],
            ]);

            foreach ($content_rule->processed_content as $song_line) {
                $song->lines()->create([
                    'line_number' => $song_line['line_number'],
                    'content_type' => $song_line['content_type'],
                    'content' => $song_line['content'],
                ]);
            }

            return $song;
        });
    }
}

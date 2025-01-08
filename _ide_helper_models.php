<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $views
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Song> $songs
 * @property-read int|null $songs_count
 * @method static \Database\Factories\ArtistFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Artist whereViews($value)
 */
	class Artist extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property int|null $simplified_chord_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ChordShape> $shapes
 * @property-read int|null $shapes_count
 * @property-read Chord|null $simplifiedChord
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chord query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chord whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chord whereSimplifiedChordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Chord whereUpdatedAt($value)
 */
	class Chord extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $chord_id
 * @property string $frets
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Chord $chord
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChordShape newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChordShape newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChordShape query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChordShape whereChordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChordShape whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChordShape whereFrets($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChordShape whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChordShape whereUpdatedAt($value)
 */
	class ChordShape extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $song_line_id
 * @property int $chord_id
 * @property int $position
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Chord $chord
 * @method static \Database\Factories\LineChordFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LineChord newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LineChord newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LineChord query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LineChord whereChordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LineChord whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LineChord whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LineChord wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LineChord whereSongLineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LineChord whereUpdatedAt($value)
 */
	class LineChord extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property int $artist_id
 * @property string|null $key
 * @property int $views
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SongLine> $lines
 * @property-read int|null $lines_count
 * @method static \Database\Factories\SongFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Song newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Song newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Song query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Song whereArtistId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Song whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Song whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Song whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Song whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Song whereSlug($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Song whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Song whereViews($value)
 */
	class Song extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $song_id
 * @property int $sequence
 * @property string $lyrics
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LineChord> $chords
 * @property-read int|null $chords_count
 * @method static \Database\Factories\SongLineFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SongLine newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SongLine newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SongLine query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SongLine whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SongLine whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SongLine whereLyrics($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SongLine whereSequence($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SongLine whereSongId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SongLine whereUpdatedAt($value)
 */
	class SongLine extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection<int, \Illuminate\Notifications\DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 */
	class User extends \Eloquent {}
}


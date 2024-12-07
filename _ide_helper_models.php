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
 * @property int $lyric_line_id
 * @property int $chord_id
 * @property int $position
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Chord $chord
 * @method static \Database\Factories\ChordPlacementFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChordPlacement newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChordPlacement newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChordPlacement query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChordPlacement whereChordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChordPlacement whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChordPlacement whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChordPlacement whereLyricLineId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChordPlacement wherePosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChordPlacement whereUpdatedAt($value)
 */
	class ChordPlacement extends \Eloquent {}
}

namespace App\Models{
/**
 * 
 *
 * @property int $id
 * @property int $song_section_id
 * @property int $chord_id
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Chord $chord
 * @method static \Database\Factories\ChordSequenceFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChordSequence newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChordSequence newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChordSequence query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChordSequence whereChordId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChordSequence whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChordSequence whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChordSequence whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChordSequence whereSongSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|ChordSequence whereUpdatedAt($value)
 */
	class ChordSequence extends \Eloquent {}
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
 * @property int $song_section_id
 * @property int $order
 * @property string $text
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ChordPlacement> $chordPlacements
 * @property-read int|null $chord_placements_count
 * @method static \Database\Factories\LyricLineFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LyricLine newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LyricLine newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LyricLine query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LyricLine whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LyricLine whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LyricLine whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LyricLine whereSongSectionId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LyricLine whereText($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|LyricLine whereUpdatedAt($value)
 */
	class LyricLine extends \Eloquent {}
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
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\SongSection> $sections
 * @property-read int|null $sections_count
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
 * @property int $is_lyrical
 * @property int $order
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\ChordSequence> $chordSequence
 * @property-read int|null $chord_sequence_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\LyricLine> $lyricLines
 * @property-read int|null $lyric_lines_count
 * @method static \Database\Factories\SongSectionFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SongSection newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SongSection newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SongSection query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SongSection whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SongSection whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SongSection whereIsLyrical($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SongSection whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SongSection whereSongId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|SongSection whereUpdatedAt($value)
 */
	class SongSection extends \Eloquent {}
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


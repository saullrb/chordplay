import { useSongStore } from '@/Stores/songStore';

export function useChordTransposer() {
    const NUM_OF_KEYS = 12;
    const songStore = useSongStore();

    function transposeChord(chord) {
        const notes = [
            'C',
            'C#',
            'D',
            'Eb',
            'E',
            'F',
            'F#',
            'G',
            'Ab',
            'A',
            'Bb',
            'B',
        ];

        function transposeNote(note) {
            const index = notes.indexOf(note);
            const newIndex =
                (index + songStore.keyOffset - songStore.capoPosition + 12) %
                12;

            return notes[newIndex];
        }

        function parseChordPart(part) {
            let i = 1;
            if (part[1] === '#' || part[1] === 'b') {
                i++;
            }

            const root = part.slice(0, i);
            const suffix = part.slice(i);
            return transposeNote(root) + suffix;
        }

        let parsedChord = parseChordPart(chord);

        if (chord.includes('/')) {
            const [main, bass] = chord.split('/');

            parsedChord = parseChordPart(main) + '/' + transposeNote(bass);
        }

        // Only add to missingChords if displaying chord diagrams
        if (!(parsedChord in songStore.chords)) {
            songStore.addMissingChord(parsedChord);
        }


        return parsedChord;
    }

    function transposeUp() {
        transposeKey(1);
    }

    function transposeDown() {
        transposeKey(-1);
    }

    function transposeKey(halfSteps) {
        const currentIndex = songStore.availableKeys.indexOf(
            songStore.currentSongKey,
        );
        const newIndex = (currentIndex + halfSteps + NUM_OF_KEYS) % NUM_OF_KEYS;

        songStore.currentSongKey = songStore.availableKeys[newIndex];

        const originalSongKeyIndex = songStore.availableKeys.indexOf(
            songStore.originalSongKey,
        );

        songStore.keyOffset = newIndex - originalSongKeyIndex;
    }

    function addCapo(position) {
        songStore.capoPosition = position;
    }

    return { transposeChord, transposeUp, transposeDown, addCapo };
}

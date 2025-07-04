import {
    addMissingChord,
    getAvailableKeysRef,
    getCapoPositionRef,
    getChordsRef,
    getCurrentSongKeyRef,
    getKeyOffsetRef,
    getOriginalSongKeyRef,
    setCapoPosition,
    setCurrentSongKey,
    setKeyOffset,
} from '@/Stores/songStore';

const NUM_OF_KEYS = 12;

const availableKeys = getAvailableKeysRef();
const keyOffset = getKeyOffsetRef();
const currentSongKey = getCurrentSongKeyRef();
const capoPosition = getCapoPositionRef();
const chords = getChordsRef();

export function useChordTransposer() {
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
                (index + keyOffset.value - capoPosition.value + 12) % 12;

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

        let parsedCord = parseChordPart(chord);

        if (chord.includes('/')) {
            const [main, bass] = chord.split('/');

            parsedCord = parseChordPart(main) + '/' + transposeNote(bass);
        }

        if (!(parsedCord in chords.value)) {
            addMissingChord(parsedCord);
        }

        return parsedCord;
    }

    function transposeUp() {
        transposeKey(1);
    }

    function transposeDown() {
        transposeKey(-1);
    }

    function transposeKey(halfSteps) {
        const currentIndex = availableKeys.value.indexOf(currentSongKey.value);
        const newIndex = (currentIndex + halfSteps + NUM_OF_KEYS) % NUM_OF_KEYS;

        setCurrentSongKey(availableKeys.value[newIndex]);

        const originalSongKeyIndex = availableKeys.value.indexOf(
            getOriginalSongKeyRef().value,
        );

        setKeyOffset(newIndex - originalSongKeyIndex);
    }

    function addCapo(position) {
        setCapoPosition(position);
    }

    return { transposeChord, transposeUp, transposeDown, addCapo };
}

import {
    getAvailableKeysRef,
    getCapoPositionRef,
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

export function useChordTransposer() {
    function transposeChord(chord) {
        const sharpNotes = [
            'C',
            'C#',
            'D',
            'D#',
            'E',
            'F',
            'F#',
            'G',
            'G#',
            'A',
            'A#',
            'B',
        ];
        const flatNotes = [
            'C',
            'Db',
            'D',
            'Eb',
            'E',
            'F',
            'Gb',
            'G',
            'Ab',
            'A',
            'Bb',
            'B',
        ];
        const flatKeys = ['Bb', 'Eb', 'Ab', 'Db', 'Gb', 'Cb'];

        const useFlats = flatKeys.includes(chord);

        function getIndex(note) {
            if (useFlats) {
                return flatNotes.indexOf(note);
            }

            return sharpNotes.indexOf(note);
        }

        function normalize(note) {
            const map = {
                Db: 'C#',
                Eb: 'D#',
                Gb: 'F#',
                Ab: 'G#',
                Bb: 'A#',
            };
            return map[note] || note;
        }

        function transposeNote(note) {
            const normalized = normalize(note);
            const index = getIndex(normalized);
            const newIndex =
                (index + keyOffset.value - capoPosition.value + 12) % 12;

            return useFlats ? flatNotes[newIndex] : sharpNotes[newIndex];
        }

        function parseChordPart(part) {
            let i = 1;
            if (part[1] === '#' || part[1] === 'b') i++;
            const root = part.slice(0, i);
            const suffix = part.slice(i);
            return transposeNote(root) + suffix;
        }

        if (chord.includes('/')) {
            const [main, bass] = chord.split('/');
            return parseChordPart(main) + '/' + transposeNote(bass);
        }

        return parseChordPart(chord);
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

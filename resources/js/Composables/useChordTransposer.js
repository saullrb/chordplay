export function useChordTransposer() {
    function transposeChord(chord, keyOffset) {
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
            const sharpIndex = sharpNotes.indexOf(note);
            if (sharpIndex !== -1) return sharpIndex;
            return flatNotes.indexOf(note);
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
            if (index === -1) return note;
            const newIndex = (index + keyOffset + 12) % 12;
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

    return { transposeChord };
}

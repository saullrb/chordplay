<script setup>
const props = defineProps({
    content: { type: Array, required: true },
    availableKeys: { type: Array, required: true },
    validChords: { type: Object, required: true },
    originalKey: { type: String, required: true },
    keyOffset: { type: Number, required: true },
});

function getChordType(chord) {
    for (const [type, chords] of Object.entries(props.validChords)) {
        if (chords.includes(chord)) {
            return type;
        }
    }

    return null;
}

function findNoteIndex(note) {
    return props.availableKeys.findIndex((n) => n === note);
}

function transposeChord(chord) {
    if (!chord) return chord;

    const chordType = getChordType(chord);

    if (chordType === null) {
        return ' ';
    }

    const originalIndex = findNoteIndex(props.originalKey);
    const targetIndex = originalIndex + props.keyOffset;
    const semitones = targetIndex - originalIndex;

    const chordArray = props.validChords[chordType];
    const currentIndex = chordArray.findIndex((c) => c === chord);

    let newIndex = (currentIndex + semitones) % chordArray.length;
    if (newIndex < 0) {
        newIndex += chordArray.length;
    }

    return chordArray[newIndex];
}

function extractBrackets(line) {
    let newLine = '';
    let bracketContent = null;

    line.split('').forEach((char) => {
        if (char === '[') {
            bracketContent = '';
        } else if (char === ']' && bracketContent) {
            const chord = bracketContent.trim();
            const transposedChord = transposeChord(chord);

            if (transposedChord) {
                newLine += transposedChord;
            }

            bracketContent = null;
        } else if (bracketContent !== null) {
            bracketContent += char;
        } else {
            newLine += ' ';
        }
    });

    return newLine;
}
</script>

<template>
    <div
        v-for="(line, lineIndex) in content"
        :key="lineIndex"
        class="w-full font-mono text-sm leading-5 tracking-tighter"
        :class="{
            'break-after-avoid': line.content_type === 'chords',
            'break-after-auto': line.content_type !== 'chords',
        }"
    >
        <p
            v-if="line.content_type === 'chords'"
            dusk="chord-line"
            class="text-accent font-bold whitespace-pre"
        >
            {{ extractBrackets(line.content) }}
        </p>
        <p v-else-if="line.content_type === 'lyrics'" class="whitespace-pre">
            {{ line.content }}
        </p>
        <br v-else />
    </div>
</template>

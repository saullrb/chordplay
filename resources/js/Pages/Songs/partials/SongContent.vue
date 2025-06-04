<script setup>
const props = defineProps({
    content: Array,
    available_keys: Array,
    valid_chords: Object,
    original_key: String,
    key_offset: Number
});

function getChordType(chord) {
    for (const [type, chords] of Object.entries(props.valid_chords)) {
        if (chords.includes(chord)) {
            return type;
        }
    }

    return null;
}

function findNoteIndex(note) {
    return props.available_keys.findIndex((n) => n === note);
}

function transposeChord(chord) {
    if (!chord) return chord;

    const chord_type = getChordType(chord);

    if (chord_type === null) {
        return ' ';
    }

    const original_index = findNoteIndex(props.original_key);
    const target_index = original_index + props.key_offset;
    const semitones = target_index - original_index;

    const chord_array = props.valid_chords[chord_type];
    const current_index = chord_array.findIndex((c) => c === chord);

    let new_index = (current_index + semitones) % chord_array.length;
    if (new_index < 0) {
        new_index += chord_array.length;
    }

    return chord_array[new_index];
}

function extractBrackets(line) {
    let new_line = '';
    let bracket_content = null;

    line.split('').forEach((char) => {
        if (char === '[') {
            bracket_content = '';
        } else if (char === ']' && bracket_content) {
            const chord = bracket_content.trim();
            const transposed_chord = transposeChord(chord);

            if (transposed_chord) {
                new_line += transposed_chord;
            }

            bracket_content = null;
        } else if (bracket_content !== null) {
            bracket_content += char;
        } else {
            new_line += ' ';
        }
    });

    return new_line;
}

</script>

<template>
    <div v-for="(line, line_index) in content" :key="line_index"
        class="w-full font-mono text-sm leading-5 tracking-tighter" :class="{
            'break-after-avoid': line.content_type === 'chords',
            'break-after-auto': line.content_type !== 'chords'
        }">
        <p v-if="line.content_type === 'chords'" class="whitespace-pre text-yellow-600">
            {{ extractBrackets(line.content) }}
        </p>
        <p v-else-if="line.content_type === 'lyrics'" class="whitespace-pre">
            {{ line.content }}
        </p>
        <br v-else />
    </div>
</template>

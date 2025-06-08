<script setup>
const props = defineProps({
    content: String,
    valid_chords: Object,
});

function isChordValid(chord) {
    for (const chords of Object.values(props.valid_chords)) {
        if (chords.includes(chord)) {
            return chord;
        }
    }

    return null;
}

function separateLines() {
    const lines = props.content.split('\n').map((line) => {
        const trimmed_line = line.trim();
        if (trimmed_line.startsWith('[')) {
            return { content_type: 'chords', content: line }
        } else {
            return { content_type: 'lyrics', content: trimmed_line }
        }
    })

    return lines
}

function extractBrackets(line) {
    let new_line = '';
    let bracket_content = null;

    line.split('').forEach((char) => {
        if (char === '[') {
            bracket_content = '';
        } else if (char === ']' && bracket_content) {
            const chord = bracket_content.trim();
            const valid_chord = isChordValid(chord);

            if (valid_chord) {
                new_line += valid_chord;
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
    <div v-for="(line, line_index) in separateLines()" :key="line_index"
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

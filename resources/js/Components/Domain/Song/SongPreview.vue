<script setup>
const props = defineProps({
    content: String,
});

function separateLines() {
    const lines = props.content.split('\n').map((line) => {
        const trimmed_line = line.trim();
        if (trimmed_line.startsWith('[')) {
            return { content_type: 'chords', content: line };
        } else if (trimmed_line === '') {
            return { content_type: 'empty', content: trimmed_line };
        } else {
            return { content_type: 'lyrics', content: trimmed_line };
        }
    });

    return lines;
}

function extractBrackets(line) {
    let new_line = '';
    let bracket_content = null;

    line.split('').forEach((char) => {
        if (char === '[') {
            bracket_content = '';
        } else if (char === ']' && bracket_content) {
            const chord = bracket_content.trim();

            new_line += chord;

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
    <div
        v-for="(line, line_index) in separateLines()"
        :key="line_index"
        class="w-full font-mono text-sm leading-5 tracking-tighter"
        :class="{
            'break-after-avoid': line.content_type === 'chords',
            'break-after-auto': line.content_type !== 'chords',
        }"
    >
        <p
            v-if="line.content_type === 'chords'"
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

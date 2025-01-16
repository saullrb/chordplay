<script setup>
const props = defineProps({
    content: String,
    valid_chords: Array,
});

function extractBrackets(line) {
    let new_line = '';
    let bracket_content = null;

    line.split('').forEach((char) => {
        if (char === '[') {
            bracket_content = '';
        } else if (char === ']' && bracket_content) {
            const chord = bracket_content.trim();

            if (props.valid_chords?.includes(chord)) {
                new_line += chord;
                bracket_content = null;
            }
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
        v-for="(line, line_index) in content?.split('\n')"
        :key="line_index"
        class="w-full font-mono text-sm leading-5 tracking-tighter"
    >
        <div class="break-inside-avoid">
            <p
                v-if="line.trim().startsWith('[')"
                class="whitespace-pre text-yellow-600"
            >
                {{ extractBrackets(line) }}
            </p>
            <p v-else-if="line" class="whitespace-pre">
                {{ line }}
            </p>
            <br v-else />
        </div>
    </div>
</template>

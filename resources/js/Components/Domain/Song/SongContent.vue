<script setup>
import { useChordTransposer } from '@/Composables/useChordTransposer';

const props = defineProps({
    content: { type: Array, required: true },
    keyOffset: { type: Number, required: true },
});

const { transposeChord } = useChordTransposer();

function extractBrackets(line) {
    let result = '';
    let token = '';
    let inside = false;

    for (const char of line) {
        if (char === '[') {
            inside = true;
            token = '';
        } else if (char === ']' && inside) {
            result += transposeChord(token, props.keyOffset);
            inside = false;
            token = '';
        } else if (inside && char !== ' ') {
            token += char;
        } else if (!inside && char === ' ') {
            result += char;
        }
    }
    return result;
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

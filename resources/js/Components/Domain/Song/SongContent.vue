<script setup>
import { useChordTransposer } from '@/Composables/useChordTransposer';

const props = defineProps({
    content: { type: Array, required: true },
});

const { transposeChord } = useChordTransposer();

function parseChordLine(line) {
    let result = [];
    let token = '';
    let inside = false;

    for (const char of line) {
        if (char === '[') {
            inside = true;
            token = '';
        } else if (char === ']' && inside) {
            result.push({
                type: 'chord',
                value: transposeChord(token),
            });
            inside = false;
            token = '';
        } else if (inside && char !== ' ') {
            token += char;
        } else if (!inside && char === ' ') {
            result.push({ type: 'text', value: char });
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
            <template
                v-for="(part, i) in parseChordLine(line.content)"
                :key="i"
            >
                <div
                    v-if="part.type === 'chord'"
                >
                        {{ part.value }}
                </div>
                <template v-else>{{ part.value }}</template>
            </template>
        </p>
        <p v-else-if="line.content_type === 'lyrics'" class="whitespace-pre">
            {{ line.content }}
        </p>
        <br v-else />
    </div>
</template>

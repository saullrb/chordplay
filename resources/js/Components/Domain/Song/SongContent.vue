<script setup>
import { useChordTransposer } from '@/Composables/useChordTransposer';
import {
    addChords,
    clearMissingChords,
    getChordsRef,
    getMissingChordsRef,
} from '@/Stores/songStore';
import axios from 'axios';
import { onUpdated } from 'vue';
import ChordDiagram from './ChordDiagram.vue';

defineProps({
    content: { type: Array, required: true },
});

const { transposeChord } = useChordTransposer();
const chords = getChordsRef();
const missingChords = getMissingChordsRef();

function parseChordLine(line) {
    let result = [];
    let token = '';
    let inside = false;

    for (const char of line) {
        if (char === '[') {
            inside = true;
            token = '';
        } else if (char === ']' && inside) {
            const transposedChord = transposeChord(token);
            result.push({
                type: 'chord',
                value: transposedChord,
                midi: chords.value[transposedChord]?.midi ?? [],
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

onUpdated(() => {
    if (missingChords.value.size > 0) {
        try {
            // TODO: add a debounce here
            axios
                .get('/api/chords', {
                    params: {
                        chords: Array.from(missingChords.value),
                    },
                })
                .then((response) => {
                    addChords(response.data);
                });

            clearMissingChords();
        } catch (error) {
            console.log('error loading chord diagrams', error);
        }
    }
});
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
                    class="dropdown dropdown-hover dropdown-top dropdown-center"
                >
                    <div tabindex="0" role="button" class="cursor-pointer">
                        {{ part.value }}
                    </div>

                    <ChordDiagram
                        :chord="chords[part.value] ?? {}"
                        :chord-name="part.value"
                        class="dropdown-content bg-base-200 group text-base-content border-base-content/20 relative z-50 inline-block rounded border shadow"
                    />
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

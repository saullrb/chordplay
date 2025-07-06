<script setup>
import { useChordTransposer } from '@/Composables/useChordTransposer';
import {
    addChords,
    clearMissingChords,
    getChordsRef,
    getMissingChordsRef,
} from '@/Stores/songStore';
import { debounce } from '@/utils/debounce';
import axios from 'axios';
import { onUpdated } from 'vue';
import ChordDiagramCarousel from './ChordDiagramCarousel.vue';

defineProps({
    content: { type: Array, required: true },
    showDiagrams: { type: Boolean, default: false },
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

function fetchMissingChords() {
    if (missingChords.value.size === 0) {
        return;
    }

    try {
        axios
            .get('/api/chords', {
                params: { chords: Array.from(missingChords.value) },
            })
            .then(function (res) {
                addChords(res.data);
                clearMissingChords();
            });
    } catch (err) {
        console.log('error loading chord diagrams', err);
    }
}

onUpdated(debounce(fetchMissingChords, 500));
</script>

<template>
    <div
        v-for="(line, lineIndex) in content"
        :key="lineIndex"
        class="w-full font-mono text-sm leading-5 tracking-tighter"
        :class="
            line.content_type === 'chords'
                ? 'break-after-avoid'
                : 'break-after-auto'
        "
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
                <template v-if="part.type === 'chord' && showDiagrams">
                    <span
                        class="dropdown dropdown-hover dropdown-top dropdown-center"
                    >
                        <span
                            tabindex="0"
                            role="button"
                            class="cursor-pointer"
                            :dusk="`chord-${part.value}`"
                        >
                            {{ part.value }}
                        </span>
                        <div
                            class="dropdown-content bg-base-200 text-base-content border-base-content/20 relative z-50 flex items-center gap-2 rounded border p-2 shadow"
                        >
                            <ChordDiagramCarousel
                                :chord-positions="chords[part.value] ?? []"
                                :chord-name="part.value"
                            />
                        </div>
                    </span>
                </template>
                <template v-else>{{ part.value }}</template>
            </template>
        </p>

        <p v-else-if="line.content_type === 'lyrics'" class="whitespace-pre">
            {{ line.content }}
        </p>

        <br v-else />
    </div>
</template>

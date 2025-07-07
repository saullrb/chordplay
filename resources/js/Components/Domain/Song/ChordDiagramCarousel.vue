<script setup>
import {
    ChevronLeftIconSolid,
    ChevronRightIconSolid,
} from '@/Components/UI/Icons';
import { useSongStore } from '@/Stores/songStore';
import { computed } from 'vue';
import ChordDiagram from './ChordDiagram.vue';

const props = defineProps({
    chordName: {
        type: String,
        required: true,
    },
});

const songStore = useSongStore();

const currentShapeIndex = computed({
    get() {
        const chord = songStore.chords[props.chordName];
        if (!chord || !chord.shapes?.length) return 0;

        const index = chord.shapes.findIndex(
            (s) => s.id === chord.defaultShapeId,
        );
        return index !== -1 ? index : 0;
    },
    set(index) {
        const chord = songStore.chords[props.chordName];
        if (!chord || !chord.shapes?.length) return;

        const shape = chord.shapes[index];
        if (shape) {
            chord.defaultShapeId = shape.id;
        }
    },
});

function prev() {
    const len = songStore.chords[props.chordName].shapes.length;
    currentShapeIndex.value = (currentShapeIndex.value - 1 + len) % len;
}

function next() {
    const len = songStore.chords[props.chordName].shapes.length;
    currentShapeIndex.value = (currentShapeIndex.value + 1) % len;
}
</script>

<template>
    <button
        class="btn btn-ghost"
        :disabled="!songStore.chords[props.chordName]?.shapes?.length"
        @click="prev"
    >
        <ChevronLeftIconSolid class="size-3" />
    </button>
    <ChordDiagram :chord-name="chordName" />
    <button
        class="btn btn-ghost"
        :disabled="!songStore.chords[props.chordName]?.shapes?.length"
        @click="next"
    >
        <ChevronRightIconSolid class="size-3" />
    </button>
</template>

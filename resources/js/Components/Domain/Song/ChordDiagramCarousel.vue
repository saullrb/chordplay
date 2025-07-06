<script setup>
import {
    ChevronLeftIconSolid,
    ChevronRightIconSolid,
} from '@/Components/UI/Icons';
import { ref } from 'vue';
import ChordDiagram from './ChordDiagram.vue';

const props = defineProps({
    chordPositions: {
        type: Array,
        default: () => [],
    },
    chordName: {
        type: String,
        required: true,
    },
});

const currentChordIndex = ref(0);

function prev() {
    currentChordIndex.value =
        (currentChordIndex.value - 1 + props.chordPositions.length) %
        props.chordPositions.length;
}
function next() {
    currentChordIndex.value =
        (currentChordIndex.value + 1 + props.chordPositions.length) %
        props.chordPositions.length;
}
</script>
<template>
    <!-- TODO: sync same chords -->
    <button class="btn btn-ghost" @click="prev">
        <ChevronLeftIconSolid class="size-3" />
    </button>
    <ChordDiagram
        :chord-position="chordPositions[currentChordIndex]"
        :chord-name="chordName"
    />
    <button class="btn btn-ghost" @click="next">
        <ChevronRightIconSolid class="size-3" />
    </button>
</template>

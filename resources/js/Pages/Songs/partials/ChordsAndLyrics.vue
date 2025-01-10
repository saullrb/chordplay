<script setup>
defineProps({
    lines: Array,
});

function formatChordLine(chords) {
    const chordLine = Array(100).fill(' ');

    chords.forEach((chord) => {
        chordLine[chord.position] = chord.name;
    });

    return chordLine.join('');
}
</script>

<template>
    <div
        v-for="(line, line_index) in lines"
        :key="line_index"
        class="w-full font-mono text-sm leading-5 tracking-tighter"
    >
        <div class="break-inside-avoid">
            <p v-if="line.chords.length" class="whitespace-pre text-yellow-600">
                {{ formatChordLine(line.chords) }}
            </p>
            <p v-if="line.lyrics" class="whitespace-pre">
                {{ line.lyrics }}
            </p>
            <br v-if="!line.lyrics && !line.chords.length" />
        </div>
    </div>
</template>

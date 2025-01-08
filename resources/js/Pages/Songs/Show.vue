<script setup>
import Container from '@/Components/Container.vue';
import NavBar from '@/Components/NavBar.vue';
import PageHeader from '@/Components/PageHeader.vue';
import TextLink from '@/Components/TextLink.vue';
import { ref } from 'vue';

defineProps({
    song: Object,
    artist: Object,
});

const isDualColumn = ref(false);

function formatChordLine(lineLength, chords) {
    const chordLine = Array(lineLength).fill(' ');

    chords.forEach((chord) => {
        chordLine[chord.position] = chord.name;
    });

    return chordLine.join('');
}
</script>

<template>
    <Head :title="`${song.name} - ${artist.name}`" />
    <NavBar />

    <Container>
        <header>
            <PageHeader :title="song.name" />
            <TextLink :href="route('artists.show', artist)">
                {{ artist.name }}
            </TextLink>
            <div class="my-6 flex items-center gap-1 text-sm dark:text-white">
                <span>Key: </span>
                <span class="text-yellow-600">
                    {{ song.key }}
                </span>
            </div>
            <button
                @click="isDualColumn = !isDualColumn"
                class="rounded-md border border-gray-300 bg-transparent px-3 py-1.5 text-sm text-gray-700 transition-colors duration-200 hover:bg-gray-300 hover:text-gray-900 dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white"
                :class="{
                    'border-gray-400 bg-gray-300 text-yellow-600 dark:border-gray-600 dark:bg-gray-800 dark:text-yellow-600':
                        isDualColumn,
                }"
            >
                <i class="fa-solid fa-table-columns mr-1"></i>
                Dual Column
            </button>
        </header>
        <main
            class="mt-6 dark:text-white"
            :class="{
                'columns-2 gap-8': isDualColumn,
            }"
        >
            <section
                v-for="line in song.lines"
                :key="line.id"
                class="font-mono text-sm leading-5 tracking-tighter"
            >
                <br v-if="!line.lyrics" />
                <span class="whitespace-pre text-yellow-600">
                    {{ formatChordLine(line.lyrics.length, line.chords) }}
                </span>
                <p class="whitespace-nowrap">
                    {{ line.lyrics }}
                </p>
            </section>
        </main>
    </Container>
</template>

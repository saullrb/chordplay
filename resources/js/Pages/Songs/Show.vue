<script setup>
import Container from '@/Components/Container.vue';
import NavBar from '@/Components/NavBar.vue';
import PageHeader from '@/Components/PageHeader.vue';
import TextLink from '@/Components/TextLink.vue';

defineProps({
    song: Object,
    artist: Object,
});

function formatChordLine(lineLength, chords) {
    const chordLine = Array(lineLength).fill(' ');

    chords.forEach((chord) => {
        chordLine[chord.position] = chord.name;
    });

    return chordLine.join('');
}
</script>

<template>
    <NavBar />

    <Head :title="`${song.name} - ${artist.name}`" />
    <Container>
        <PageHeader :title="song.name" />
        <TextLink :href="route('artists.show', artist)">
            {{ artist.name }}
        </TextLink>

        {{ console.log(song) }}
        <section class="mt-6 dark:text-white">
            <p class="mb-6 flex items-center gap-1 text-sm">
                <span>Key: </span>
                <span class="text-yellow-600">
                    {{ song.key }}
                </span>
            </p>

            <div
                v-for="section in song.sections"
                class="leading-5"
                :key="section.id"
            >
                <div class="" v-if="section.is_lyrical">
                    <div v-for="line in section.content" :key="line.id">
                        <span class="whitespace-pre font-mono text-yellow-600">
                            {{ formatChordLine(line.text.length, line.chords) }}
                        </span>
                        <p class="font-mono">
                            {{ line.text }}
                        </p>
                    </div>
                </div>
                <div class="leading-3" v-else>
                    <span
                        v-for="chord in section.content"
                        class="mr-2 font-mono text-yellow-600"
                        :key="chord.id"
                    >
                        {{ chord.name }}
                    </span>
                </div>
                <br />
            </div>
        </section>
    </Container>
</template>

<script setup>
import Container from '@/Components/Container.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import NavBar from '@/Components/NavBar.vue';
import PageHeader from '@/Components/PageHeader.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextLink from '@/Components/TextLink.vue';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import ChordsAndLyrics from './partials/ChordsAndLyrics.vue';

const form = useForm({
    name: '',
    key: null,
    song_lines: [],
});

const props = defineProps({
    song_keys: Array,
    artist: Object,
    available_chords: Array,
});

const lyrics = ref('');

function extractChords(line) {
    const chords = [];
    let bracket_content = '';
    let position = 0;

    for (const char of line) {
        if (char === '[') {
            bracket_content = '';
        } else if (char === ']' && bracket_content) {
            const chord = bracket_content.trim();

            if (props.available_chords.includes(chord)) {
                chords.push({ name: chord, position });
                bracket_content = null;
                position++;
            }
        } else if (bracket_content !== null) {
            bracket_content += char;
        } else {
            position++;
        }
    }

    return chords;
}

function processLyrics() {
    const lines = lyrics.value.split('\n').map((line) => line.trim());
    const song_lines = [];
    let sequence = 1;

    lines.forEach((current, index) => {
        const previous = lines[index - 1];
        const next = lines[index + 1];

        const isChordLine = (line) => line?.startsWith('[');

        // Current line is chords with no lyrics bellow
        if (isChordLine(current) && (!next || isChordLine(next))) {
            song_lines.push({
                sequence: sequence++,
                lyrics: '',
                chords: extractChords(current),
            });
        } else if (current && !isChordLine(current) && isChordLine(previous)) {
            // Current line is lyrics with chords above
            song_lines.push({
                sequence: sequence++,
                lyrics: current,
                chords: extractChords(previous),
            });
        } else if (!isChordLine(current)) {
            // Current line is lyrics or blank without chords:
            song_lines.push({
                sequence: sequence++,
                lyrics: current,
                chords: [],
            });
        }
    });

    form.song_lines = song_lines;
}

function chordErrors() {
    if (!form.errors) return;

    return Object.keys(form.errors)
        .filter((key) => key !== 'name' && key !== 'key')
        .map((key) => form.errors[key]);
}

function submit() {
    form.post(route('artists.songs.store', props.artist));
}
</script>

<template>
    <Head :title="`${artist.name} - Add Song`" />
    <NavBar />

    <main class="mt-6">
        <Container>
            <PageHeader title="Add song" />
            <TextLink :href="route('artists.show', artist)">
                {{ artist.name }}
            </TextLink>
            <div class="mt-6 grid grid-cols-2 justify-between gap-12 py-6">
                <form
                    @submit.prevent="submit"
                    class="flex w-full flex-col gap-2"
                >
                    <div class="flex flex-col gap-1">
                        <InputLabel for="key" value="Key" />
                        <select
                            class="bg-white p-2 dark:bg-gray-800 dark:text-white"
                            v-model="form.key"
                            id="key"
                        >
                            <option
                                :key="key"
                                v-for="key in song_keys"
                                :value="key"
                                class="px-2 dark:bg-gray-800 dark:text-white"
                            >
                                {{ key }}
                            </option>
                        </select>
                        <InputError :message="form.errors.key" />
                    </div>
                    <div class="flex flex-col gap-1">
                        <InputLabel for="name" value="Name" />
                        <input
                            class="px-2 py-1 dark:bg-gray-800 dark:text-white"
                            type="text"
                            v-model="form.name"
                            id="name"
                        />
                        <InputError :message="form.errors.name" />
                    </div>
                    <div class="flex flex-col gap-1">
                        <InputLabel for="lyrics" value="Lyrics" />
                        <textarea
                            @input="processLyrics"
                            v-model="lyrics"
                            id="lyrics"
                            class="h-20 resize-y px-2 font-mono text-sm leading-5 tracking-tighter dark:bg-gray-800 dark:text-white"
                        >
                        </textarea>
                    </div>
                    <InputError
                        v-for="(error, error_index) in chordErrors()"
                        :key="error_index"
                        :message="error"
                    />
                    <div class="mt-4 flex justify-end">
                        <PrimaryButton
                            class="w-20"
                            :disabled="form.processing"
                            type="submit"
                        >
                            Submit
                        </PrimaryButton>
                    </div>
                </form>

                <section class="dark:text-white" v-if="form.song_lines.length">
                    <h3 class="text-md mb-6">Preview</h3>
                    <ChordsAndLyrics :lines="form.song_lines" />
                </section>
            </div>
        </Container>
    </main>
</template>

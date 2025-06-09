<script setup>
import Container from '@/Components/Container.vue';
import NavBar from '@/Components/NavBar.vue';
import PageHeader from '@/Components/PageHeader.vue';
import TextLink from '@/Components/TextLink.vue';
import SongForm from './partials/SongForm.vue';
import { useForm } from '@inertiajs/vue3';
import SongPreview from './partials/SongPreview.vue';

const props = defineProps({
    valid_chords: Object,
    artist: Object,
    available_keys: Array,
    song: Object,
});

const form = useForm({
    name: props.song.name,
    key: props.song.key,
    content: props.song.content,
});

function submitForm() {
    form.patch(
        route('artists.songs.update', {
            artist: props.artist,
            song: props.song,
        }),
    );
}

</script>

<template>

    <Head :title="`${artist.name} - ${song.name} - Edit`" />

    <NavBar />

    <main class="mt-6">
        <Container>
            <PageHeader title="Edit song" />

            <TextLink :href="route('artists.show', artist)">
                {{ artist.name }}
            </TextLink>

            <div class="mt-6 grid grid-cols-2 justify-between gap-12 py-6">
                <SongForm :available_keys="available_keys" :valid_chords="valid_chords" :initial_data="form"
                    submit_label="Update" @submit="submitForm" />

                <section class="dark:text-white">
                    <h3 class="text-md mb-6">Preview</h3>
                    <SongPreview :content="form.content" :valid_chords="valid_chords" />
                </section>
            </div>
        </Container>
    </main>
</template>

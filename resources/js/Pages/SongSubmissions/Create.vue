<script setup>
import Container from '@/Components/Container.vue';
import NavBar from '@/Components/NavBar.vue';
import PageHeader from '@/Components/PageHeader.vue';
import TextLink from '@/Components/TextLink.vue';
import { useForm } from '@inertiajs/vue3';
import SongForm from './partials/SongForm.vue';
import SongPreview from './partials/SongPreview.vue';

const props = defineProps({
    available_keys: Array,
    artist: Object,
});

const form = useForm({
    name: '',
    key: null,
    content: '',
});

function submitForm() {
    form.post(route('song_submissions.store', { artist: props.artist.slug }));
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
                <SongForm
                    :available_keys="available_keys"
                    :initial_data="form"
                    submit_label="Add"
                    @submit="submitForm"
                />

                <section class="dark:text-white">
                    <h3 class="text-md mb-6">Preview</h3>
                    <SongPreview :content="form.content" />
                </section>
            </div>
        </Container>
    </main>
</template>

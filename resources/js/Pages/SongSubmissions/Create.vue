<script setup>
import SongForm from '@/Components/Domain/Song/SongForm.vue';
import SongPreview from '@/Components/Domain/Song/SongPreview.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    artist: {
        type: Object,
        required: true,
    },
    availableKeys: {
        type: Array,
        required: true,
    },
});

const form = useForm({
    name: '',
    key: null,
    content: '',
});

function submitForm() {
    form.post(route('song-submissions.store', { artist: props.artist.slug }));
}
</script>

<template>
    <Head :title="`${artist.name} - Add Song`" />

    <AppLayout dusk="song-submissions-create-page">
        <template #header>
            <PageHeader title="Add song" />

            <Link
                :href="route('artists.show', props.artist)"
                class="text-base-content/70 hover:text-base-content/90"
            >
                {{ artist.name }}
            </Link>
        </template>

        <div
            class="mt-6 grid grid-cols-1 justify-between gap-12 py-6 lg:grid-cols-2"
        >
            <SongForm
                :available-keys="availableKeys"
                :initial-data="form"
                @submit="submitForm"
            />

            <section v-if="form.content" class="dark:text-white">
                <h2 class="mb-6">Song Preview</h2>
                <SongPreview :content="form.content" />
            </section>
        </div>
    </AppLayout>
</template>

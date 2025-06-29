<script setup>
import SongForm from '@/Components/Domain/Song/SongForm.vue';
import SongPreview from '@/Components/Domain/Song/SongPreview.vue';
import PageHeader from '@/Components/PageHeader.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, useForm } from '@inertiajs/vue3';

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

    <AppLayout>
        <template #header>
            <PageHeader title="Add song" />

            <Link
                :href="route('artists.show', props.artist)"
                class="text-base-content/50 dark:text-base-content/30 hover:text-base-content/70 dark:hover:text-base-content/50"
            >
                {{ artist.name }}
            </Link>
        </template>

        <div
            class="mt-6 grid grid-cols-1 justify-between gap-12 py-6 lg:grid-cols-2"
        >
            <SongForm
                :available_keys="available_keys"
                :initial_data="form"
                @submit="submitForm"
            />

            <section v-if="form.content" class="dark:text-white">
                <h2 class="mb-6">Song Preview</h2>
                <SongPreview :content="form.content" />
            </section>
        </div>
    </AppLayout>
</template>

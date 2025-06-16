<script setup>
import PageHeader from '@/Components/PageHeader.vue';
import TextLink from '@/Components/TextLink.vue';
import SongForm from './partials/SongForm.vue';
import { useForm } from '@inertiajs/vue3';
import SongPreview from './partials/SongPreview.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    artist: Object,
    available_keys: Array,
    song_submission: Object,
});

const form = useForm({
    name: props.song_submission.name,
    key: props.song_submission.key,
    content: props.song_submission.content,
});

function submitForm() {
    form.patch(
        route('song_submissions.update', {
            song_submission: props.song_submission,
        }),
    );
}
</script>

<template>
    <Head
        :title="`${song_submission.artist.name} - ${song_submission.name} - Edit`"
    />

    <AppLayout>
        <template #header>
            <PageHeader title="Edit song submission" />

            <TextLink :href="route('artists.show', song_submission.artist)">
                {{ song_submission.artist.name }}
            </TextLink>
        </template>

        <div class="mt-6 grid grid-cols-2 justify-between gap-12 py-6">
            <SongForm
                :available_keys="available_keys"
                :initial_data="form"
                submit_label="Update"
                @submit="submitForm"
            />

            <section class="dark:text-white">
                <h3 class="text-md mb-6">Preview</h3>
                <SongPreview :content="form.content" />
            </section>
        </div>
    </AppLayout>
</template>

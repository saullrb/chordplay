<script setup lang="ts">
import PageHeader from '@/Components/UI/PageHeader.vue';
import SimplePagination from '@/Components/UI/SimplePagination.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import SongSubmissionTable from './partials/SongSubmissionTable.vue';

defineProps({
    submissions: {
        type: Object,
        required: true,
    },
});
</script>

<template>
    <Head title="Song Submissions" />

    <AppLayout>
        <template #header>
            <PageHeader title="Song Submissions" />
        </template>

        <div dusk="song-submissions-index" class="my-6">
            <SongSubmissionTable
                :submissions="submissions.data"
                :show-updated="true"
            />
            <SimplePagination :pagination="submissions" />
        </div>
    </AppLayout>
</template>

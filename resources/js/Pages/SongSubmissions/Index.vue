<script setup lang="ts">
import PageHeader from '@/Components/PageHeader.vue';
import SimplePagination from '@/Components/SimplePagination.vue';
import SongSubmissionTable from './partials/SongSubmissionTable.vue';
import AppLayout from '@/Layouts/AppLayout.vue';

defineProps({
    submissions: Object,
});
</script>

<template>
    <Head title="Song Submissions" />

    <AppLayout>
        <template #header>
            <PageHeader title="Song Submissions" />
        </template>

        <div class="mt-6 mb-6">
            <SongSubmissionTable :submissions="submissions.data" />
            <SimplePagination :pagination="submissions" />
        </div>
    </AppLayout>
</template>

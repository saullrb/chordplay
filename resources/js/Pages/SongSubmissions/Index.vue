<script setup lang="ts">
import Container from '@/Components/Container.vue';
import NavBar from '@/Components/NavBar.vue';
import PageHeader from '@/Components/PageHeader.vue';
import SimplePagination from '@/Components/SimplePagination.vue';
import SongSubmissionTable from './partials/SongSubmissionTable.vue';

defineProps({
    submissions: Object,
});
</script>

<template>
    <Head title="Song Submissions" />

    <NavBar />

    <main class="mt-6 mb-6">
        <Container>
            <PageHeader title="Song Submissions" />

            <div class="mt-6 mb-6">
                <SongSubmissionTable :submissions="submissions.data" />
                <SimplePagination :pagination="submissions" />
            </div>
        </Container>
    </main>
</template>

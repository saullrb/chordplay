<script setup>
import PageHeader from '@/Components/PageHeader.vue';
import { useStatusMessage } from '@/Composables/useStatusMessage';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    status: {
        type: Number,
        required: true,
    },
});
</script>

<template>
    <Head :title="useStatusMessage(status)" />

    <div class="flex h-screen flex-col items-center justify-center gap-4">
        <PageHeader :title="`${status} - ${useStatusMessage(status)}`" />
        <Link
            v-if="status !== 503"
            class="btn btn-primary"
            :href="route('home')"
        >
            HomePage
        </Link>
    </div>
</template>

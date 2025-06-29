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

    <div class="flex h-screen justify-center">
        <div class="flex items-center space-x-10">
            <div
                class="text-primary-black flex flex-col items-center space-y-2 font-mono"
            >
                <PageHeader
                    :title="`${props.status} - ${useStatusMessage(status)}`"
                />
                <Link
                    v-if="status !== 503"
                    class="block rounded-lg bg-gray-300 px-4 py-2 font-semibold text-gray-900 hover:bg-gray-500 hover:text-white dark:bg-gray-700 dark:text-white dark:hover:bg-gray-800"
                    :href="route('home')"
                >
                    HomePage
                </Link>
            </div>
        </div>
    </div>
</template>

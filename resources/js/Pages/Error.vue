<script setup>
import { Head, Link } from '@inertiajs/vue3'
import PageHeader from '@/Components/PageHeader.vue';
import { useStatusMessage } from '@/composables/status';

const props = defineProps({ status: Number });

</script>

<template>

    <Head :title="useStatusMessage(status)" />

    <div class="h-screen flex justify-center">

        <div class="flex items-center space-x-10">

            <div class="flex flex-col items-center space-y-2 font-mono text-primary-black">
                <PageHeader :title="`${props.status} - ${useStatusMessage(status)}`" />
                <Link
                    class="block rounded-lg px-4 py-2 bg-gray-300  font-semibold text-gray-900 hover:bg-gray-500 hover:text-white dark:bg-gray-700 dark:text-white dark:hover:bg-gray-800"
                    :href="route('home')">
                HomePage
                </Link>
            </div>

        </div>

    </div>

</template>

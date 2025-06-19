<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import InputError from '@/Components/InputError.vue';

const form = useForm({
    query: null,
});

function handleSubmit() {
    form.get(route('search'));
}
</script>

<template>
    <Head title="Home" />
    <AppLayout>
        <div class="mx-auto mt-40 flex w-lg flex-col">
            <div class="flex flex-col gap-4">
                <form @submit.prevent="handleSubmit" class="space-y-3">
                    <div class="flex justify-center">
                        <input
                            v-model="form.query"
                            type="text"
                            class="z-10 w-full rounded-l border-black dark:border-gray-400 dark:bg-gray-800 dark:text-white"
                            placeholder="Search songs or artists..."
                        />
                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="cursor-pointer rounded-r border border-l-0 border-black bg-white px-4 transition-colors hover:bg-gray-100 disabled:cursor-wait dark:border-gray-400 dark:bg-gray-800 dark:text-white dark:hover:bg-gray-700"
                        >
                            <i class="fa-solid fa-search"></i>
                        </button>
                    </div>
                    <InputError :message="form.errors.query" />
                </form>
            </div>
        </div>
    </AppLayout>
</template>

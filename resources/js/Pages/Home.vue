<script setup>
import { SearchIcon } from '@/Components/UI/Icons';
import InputError from '@/Components/UI/InputError.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    query: null,
});

function handleSubmit() {
    form.get(route('search'), {
        preserveState: true,
    });
}
</script>

<template>
    <Head title="Home" />
    <AppLayout>
        <div
            class="flex h-full flex-col items-center justify-start gap-6 pt-40"
        >
            <h1 class="mb-4 text-center text-2xl font-bold sm:text-3xl">
                Search songs and artists
            </h1>
            <form @submit.prevent="handleSubmit" class="w-full max-w-md">
                <label class="input input-primary validator w-full">
                    <SearchIcon class="h-[1em] opacity-50" />
                    <input
                        dusk="search-input"
                        v-model="form.query"
                        type="search"
                        required
                        placeholder="Search"
                        maxlength="100"
                        @input="form.errors.query = null"
                    />
                </label>
                <InputError :message="form.errors.query" />
            </form>
        </div>
    </AppLayout>
</template>

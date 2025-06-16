<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    results: Object,
    query: String,
    has_more_songs: Boolean,
    has_more_artists: Boolean,
});

const form = useForm({
    query: props.query ?? '',
});

function handleSubmit() {
    form.get(route('search'), {
        preserveState: true,
        preserveUrl: true,
    });
}
</script>

<template>
    <Head title="Home" />
    <AppLayout>
        <div class="mx-auto mt-40 flex w-lg flex-col">
            <div class="flex flex-col gap-4">
                <form
                    @submit.prevent="handleSubmit"
                    class="flex justify-center"
                >
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
                </form>

                <div
                    v-if="results"
                    class="mb-4 divide-y rounded bg-white shadow-lg dark:bg-gray-800 dark:text-white"
                >
                    <div v-if="results.songs.length" class="p-4">
                        <h3 class="mb-2 font-bold">Songs</h3>
                        <ul class="space-y-1">
                            <li
                                v-for="song in results.songs"
                                :key="song.id"
                                class="rounded px-2 py-1 hover:bg-gray-50 dark:hover:bg-gray-700"
                            >
                                <Link
                                    class="flex grow items-center gap-2 text-sm"
                                    :href="
                                        route('artists.songs.show', [
                                            song.artist,
                                            song.slug,
                                        ])
                                    "
                                >
                                    {{ song.name }} -
                                    {{ song.artist?.name }}
                                </Link>
                            </li>
                            <li
                                v-if="has_more_songs"
                                class="rounded px-2 py-1 hover:bg-gray-50 dark:hover:bg-gray-700"
                            >
                                <Link
                                    class="flex grow items-center gap-2 font-bold"
                                    :href="route('search.show', { query })"
                                >
                                    See More...
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <div v-if="results.artists.length" class="p-4">
                        <h3 class="mb-2 font-bold">Artists</h3>
                        <ul class="space-y-1">
                            <li
                                v-for="artist in results.artists"
                                :key="artist.id"
                                class="rounded px-2 py-1 hover:bg-gray-50 dark:hover:bg-gray-700"
                            >
                                <Link
                                    class="flex grow items-center gap-2 text-sm"
                                    :href="route('artists.show', artist)"
                                >
                                    {{ artist.name }}
                                </Link>
                            </li>
                            <li
                                v-if="has_more_artists"
                                class="rounded px-2 py-1 hover:bg-gray-50 dark:hover:bg-gray-700"
                            >
                                <Link
                                    class="flex grow items-center gap-2 text-sm font-bold"
                                    :href="route('search.show', { query })"
                                >
                                    See More...
                                </Link>
                            </li>
                        </ul>
                    </div>

                    <!-- No results -->
                    <div
                        v-if="!results.songs.length && !results.artists.length"
                        class="p-4 text-gray-500 dark:text-gray-100"
                    >
                        No results found for "{{ query }}"
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

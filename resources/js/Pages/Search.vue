<script setup>
import Container from '@/Components/Container.vue';
import NavBar from '@/Components/NavBar.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { Link } from '@inertiajs/vue3';

defineProps({
    results: Object,
    query: String,
});
</script>

<template>
    <Head :title="'Search: ' + query" />

    <NavBar />

    <Container>
        <main class="my-6 flex flex-col gap-4">
            <PageHeader :title="'Search: ' + query" />

            <div class="flex flex-col gap-4">
                <div class="text-black dark:text-white">
                    <h2 class="text-xl">Songs</h2>
                    <ul class="mt-2">
                        <li v-for="song of results.songs" :key="song.slug">
                            <Link
                                class="hover:text-yellow-600"
                                :href="
                                    route('artists.songs.show', [
                                        song.artist,
                                        song.slug,
                                    ])
                                "
                            >
                                <b>{{ song.name }}</b> -
                                {{ song.artist.name }}
                            </Link>
                        </li>
                    </ul>
                </div>

                <div class="text-black dark:text-white">
                    <h2 class="text-xl">Artists</h2>
                    <ul class="mt-2">
                        <li
                            v-for="artist of results.artists"
                            :key="artist.slug"
                            class="hover:text-yellow-600"
                        >
                            <Link :href="route('artists.show', artist)">
                                {{ artist.name }}
                            </Link>
                        </li>
                    </ul>
                </div>
            </div>
        </main>
    </Container>
</template>

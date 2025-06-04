<script setup>
import Container from '@/Components/Container.vue';
import FavoriteButton from '@/Components/FavoriteButton.vue';
import IconLink from '@/Components/IconLink.vue';
import ItemList from '@/Components/ItemList.vue';
import NavBar from '@/Components/NavBar.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    artist: Object,
    is_favorited: Boolean,
    can: {
        type: Object,
        default: () => ({
            create_song: {
                type: Boolean,
                default: false,
            },
        }),
    },
});

const is_loading = ref(false);

function handleFavorite() {
    is_loading.value = true;

    const method = props.is_favorited ? 'delete' : 'post';

    router.visit(route('artists.favorite', props.artist), {
        method,
        only: ['is_favorited'],
        preserveState: true,
        onFinish: () => {
            is_loading.value = false;
        },
        onError: () => {
            is_loading.value = false;
        },
    });
}
</script>

<template>

    <Head :title="artist.name" />

    <NavBar />

    <main class="mt-6">
        <Container>
            <div class="flex justify-between">
                <div class="flex items-center gap-4">
                    <PageHeader :title="artist.name" />
                    <FavoriteButton @favorite="handleFavorite" :is_favorited="is_favorited" :disabled="is_loading" />
                </div>

                <IconLink v-if="can.create_song" :href="route('artists.songs.create', artist)"><i
                        class="fa-solid fa-plus"></i>
                </IconLink>
            </div>
            <ItemList :items="artist.songs" :parent="{ slug: artist.slug }" showRouteName="artists.songs.show" />
        </Container>
    </main>
</template>

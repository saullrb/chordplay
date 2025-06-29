<script setup>
import FavoriteButton from '@/Components/FavoriteButton.vue';
import ItemList from '@/Components/ItemList.vue';
import LoadingButton from '@/Components/LoadingButton.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { PlusIconSolid } from '@/Components/UI/Icons';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    artist: Object,
    songs: Object,
    is_favorited: Boolean,
});

const user = usePage().props.auth.user;
const loading = ref(false);

function handleFavorite() {
    loading.value = true;

    const method = props.is_favorited ? 'delete' : 'post';

    router.visit(route('artists.favorite', props.artist), {
        method,
        only: ['is_favorited'],
        preserveState: true,
        onFinish: () => {
            loading.value = false;
        },
        onError: () => {
            loading.value = false;
        },
    });
}

const loadMoreSongs = async () => {
    if (!props.songs.next_page_url) return;

    loading.value = true;

    router.reload({
        only: ['songs'],
        data: { page: props.songs.current_page + 1 },
        preserveUrl: true,
        showProgress: true,
        onFinish: () => {
            loading.value = false;
        },
    });
};
</script>

<template>
    <Head :title="artist.name" />

    <AppLayout>
        <template #header>
            <div class="flex w-full justify-between">
                <div class="flex items-center gap-4">
                    <PageHeader :title="artist.name" />
                    <FavoriteButton
                        @favorite="handleFavorite"
                        :favorited="is_favorited"
                        :loading="loading"
                    />
                </div>
                <Link
                    v-if="user"
                    :href="route('artists.songs.create', artist)"
                    class="btn btn-primary btn-sm"
                    dusk="add-song-link"
                >
                    <PlusIconSolid class="size-5" />
                    Add Song
                </Link>
            </div>
        </template>
        <ItemList
            :items="songs.data"
            :parent="{ slug: artist.slug }"
            show_route_name="artists.songs.show"
            class="mt-6"
        />
        <div class="my-6 flex justify-center">
            <LoadingButton
                v-if="songs.next_page_url"
                :onLoadMore="loadMoreSongs"
                :loading="loading"
            >
                Load More
            </LoadingButton>
        </div>
    </AppLayout>
</template>

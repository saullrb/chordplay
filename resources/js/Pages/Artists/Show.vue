<script setup>
import FavoriteButton from '@/Components/UI/FavoriteButton.vue';
import { PlusIconSolid } from '@/Components/UI/Icons';
import ItemList from '@/Components/UI/ItemList.vue';
import LoadingButton from '@/Components/UI/LoadingButton.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import Panel from '@/Components/UI/Panel.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    artist: {
        type: Object,
        required: true,
    },
    songs: {
        type: Object,
        required: true,
    },
    isFavorited: Boolean,
});

const user = usePage().props.auth.user;
const loading = ref(false);

function handleFavorite() {
    loading.value = true;

    const method = props.isFavorited ? 'delete' : 'post';

    router.visit(route('artists.favorite', props.artist), {
        method,
        only: ['isFavorited'],
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
                        :favorited="isFavorited"
                        :loading="loading"
                        @favorite="handleFavorite"
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

        <Panel>
            <ItemList
                :items="songs.data"
                :parent="{ slug: artist.slug }"
                show-route-name="artists.songs.show"
            />
            <div class="flex justify-center">
                <LoadingButton
                    v-if="songs.next_page_url"
                    :on-load-more="loadMoreSongs"
                    :loading="loading"
                >
                    Load More
                </LoadingButton>
            </div>
        </Panel>
    </AppLayout>
</template>

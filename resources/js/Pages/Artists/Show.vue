<script setup>
import FavoriteButton from '@/Components/UI/FavoriteButton.vue';
import { PlusIconSolid } from '@/Components/UI/Icons';
import ItemList from '@/Components/UI/ItemList.vue';
import LoadingButton from '@/Components/UI/LoadingButton.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import Panel from '@/Components/UI/Panel.vue';
import ToastMessage from '@/Components/UI/ToastMessage.vue';
import { useToast } from '@/Composables/useToast';
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
const isFavorited = ref(props.isFavorited);

const { toastShow, toastMessage, toastType, toastDuration, showToast } =
    useToast();

async function handleFavorite() {
    loading.value = true;
    const method = isFavorited.value ? 'delete' : 'post';
    const routeName = isFavorited.value
        ? 'artists.unfavorite'
        : 'artists.favorite';
    const url = route(routeName, props.artist);

    try {
        await axios({ method, url });
        isFavorited.value = !isFavorited.value;
    } catch (e) {
        console.error(e);
        showToast('Failed to update favorite status', 'error');
    } finally {
        loading.value = false;
    }
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

    <AppLayout dusk="artists-show-page">
        <template #header>
            <div class="flex w-full justify-between">
                <div class="flex items-center gap-4">
                    <div>
                        <img
                            class="rounded-box size-20"
                            :src="
                                artist.profile_image_url ??
                                '/images/artist.webp'
                            "
                        />
                    </div>
                    <PageHeader :title="artist.name" />
                    <FavoriteButton
                        v-if="user"
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
                    prefetch
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
    <ToastMessage
        v-model:show="toastShow"
        :message="toastMessage"
        :type="toastType"
        :duration="toastDuration"
    />
</template>

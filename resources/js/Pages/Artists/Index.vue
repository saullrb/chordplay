<script setup>
import ItemList from '@/Components/ItemList.vue';
import LoadingButton from '@/Components/LoadingButton.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { PlusIconSolid } from '@/Components/UI/Icons';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    artists: Object,
    can: {
        type: Object,
        default: () => ({
            create_artist: {
                type: Boolean,
                default: false,
            },
        }),
    },
});

const loading = ref(false);

const loadMoreArtists = async () => {
    if (!props.artists.next_page_url) return;

    loading.value = true;

    router.reload({
        only: ['artists'],
        data: { page: props.artists.current_page + 1 },
        preserveUrl: true,
        showProgress: true,
        onFinish: () => {
            loading.value = false;
        },
    });
};
</script>

<template>
    <Head title="Artists" />

    <AppLayout>
        <template #header>
            <div class="flex w-full justify-between">
                <PageHeader title="All Artists" />

                <Link
                    v-if="can.create_artist"
                    :href="route('artists.create')"
                    class="btn btn-primary btn-sm"
                >
                    <PlusIconSolid class="size-5" />
                    Add Artist
                </Link>
            </div>
        </template>

        <ItemList
            show_route_name="artists.show"
            :items="artists.data"
            class="mt-6"
        />

        <div class="my-6 flex justify-center">
            <LoadingButton
                v-if="artists.next_page_url"
                :onLoadMore="loadMoreArtists"
                :loading="loading"
            >
                Load More
            </LoadingButton>
        </div>
    </AppLayout>
</template>

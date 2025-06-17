<script setup>
import IconLink from '@/Components/IconLink.vue';
import ItemList from '@/Components/ItemList.vue';
import LoadMoreButton from '@/Components/LoadMoreButton.vue';
import PageHeader from '@/Components/PageHeader.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';

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
    });

    loading.value = false;
};
</script>

<template>
    <Head title="Artists" />

    <AppLayout>
        <template #header>
            <div class="flex justify-between">
                <PageHeader title="All Artists" />

                <IconLink
                    v-if="can.create_artist"
                    :href="route('artists.create')"
                    ><i class="fa-solid fa-plus"></i>
                </IconLink>
            </div>
        </template>

        <ItemList showRouteName="artists.show" :items="artists.data" />

        <div class="my-6 flex justify-center">
            <LoadMoreButton
                v-if="artists.next_page_url"
                :onLoadMore="loadMoreArtists"
                :loading="loading"
            />
        </div>
    </AppLayout>
</template>

<script setup>
import { PlusIconSolid } from '@/Components/UI/Icons';
import ItemList from '@/Components/UI/ItemList.vue';
import LoadingButton from '@/Components/UI/LoadingButton.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import Panel from '@/Components/UI/Panel.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    artists: {
        type: Object,
        required: true,
    },
    can: {
        type: Object,
        default: () => ({
            createArtist: {
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
                    v-if="can.createArtist"
                    :href="route('artists.create')"
                    class="btn btn-primary btn-sm"
                    prefetch
                >
                    <PlusIconSolid class="size-5" />
                    Add Artist
                </Link>
            </div>
        </template>
        <Panel>
            <ItemList
                show-route-name="artists.show"
                :items="artists.data"
                :has-image="true"
            />

            <div class="flex justify-center">
                <LoadingButton
                    v-if="artists.next_page_url"
                    :on-load-more="loadMoreArtists"
                    :loading="loading"
                >
                    Load More
                </LoadingButton>
            </div>
        </Panel>
    </AppLayout>
</template>

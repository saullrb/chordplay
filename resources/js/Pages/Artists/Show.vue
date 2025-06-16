<script setup>
import FavoriteButton from '@/Components/FavoriteButton.vue';
import IconLink from '@/Components/IconLink.vue';
import ItemList from '@/Components/ItemList.vue';
import PageHeader from '@/Components/PageHeader.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    artist: Object,
    is_favorited: Boolean,
});

const page = usePage();
const user = page.props.auth.user;
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

    <AppLayout>
        <template #header>
            <div class="flex justify-between">
                <div class="flex items-center gap-4">
                    <PageHeader :title="artist.name" />
                    <FavoriteButton
                        @favorite="handleFavorite"
                        :is_favorited="is_favorited"
                        :disabled="is_loading"
                    />
                </div>

                <IconLink
                    v-if="user"
                    :href="route('artists.songs.create', artist)"
                    ><i class="fa-solid fa-plus"></i>
                </IconLink>
            </div>
        </template>
        <ItemList
            :items="artist.songs"
            :parent="{ slug: artist.slug }"
            showRouteName="artists.songs.show"
        />
    </AppLayout>
</template>

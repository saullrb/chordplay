<script setup>
import Container from '@/Components/Container.vue';
import IconLink from '@/Components/IconLink.vue';
import ItemList from '@/Components/ItemList.vue';
import LoadMoreButton from '@/Components/LoadMoreButton.vue';
import NavBar from '@/Components/NavBar.vue';
import PageHeader from '@/Components/PageHeader.vue';
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

const next_url = ref(props.artists.next_page_url);
const loading = ref(false);
const artists = ref(props.artists.data);

const loadMoreArtists = async () => {
    if (!next_url.value) return;

    loading.value = true;

    axios.get(next_url.value).then(({ data }) => {
        next_url.value = data.next_page_url;
        artists.value.push(...data.data);
    });

    loading.value = false;
};
</script>

<template>
    <Head title="Artists" />

    <NavBar />

    <main class="mt-6">
        <Container>
            <div class="flex justify-between">
                <PageHeader title="All Artists" />

                <IconLink
                    v-if="can.create_artist"
                    :href="route('artists.create')"
                    ><i class="fa-solid fa-plus"></i>
                </IconLink>
            </div>

            <ItemList showRouteName="artists.show" :items="artists" />

            <div class="my-6 flex justify-center">
                <LoadMoreButton
                    v-if="next_url"
                    :onLoadMore="loadMoreArtists"
                    :loading="loading"
                />
            </div>
        </Container>
    </main>
</template>

<script setup>
import Container from '@/Components/Container.vue';
import IconLink from '@/Components/IconLink.vue';
import ItemList from '@/Components/ItemList.vue';
import NavBar from '@/Components/NavBar.vue';
import PageHeader from '@/Components/PageHeader.vue';

defineProps({
    artist: Object,
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
</script>

<template>
    <Head :title="artist.name" />

    <NavBar />

    <main class="mt-6">
        <Container>
            <div class="flex justify-between">
                <PageHeader :title="artist.name" />

                <IconLink
                    v-if="can.create_song"
                    :href="route('artists.songs.create', artist)"
                    ><i class="fa-solid fa-plus"></i>
                </IconLink>
            </div>
            <ItemList
                :items="artist.songs"
                :parent="{ slug: artist.slug }"
                showRouteName="artists.songs.show"
            />
        </Container>
    </main>
</template>

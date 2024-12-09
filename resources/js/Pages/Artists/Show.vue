<script setup>
import Container from '@/Components/Container.vue';
import ItemList from '@/Components/ItemList.vue';
import NavBar from '@/Components/NavBar.vue';
import PageHeader from '@/Components/PageHeader.vue';

defineProps({
    artist: Object,
});
</script>

<template>
    <Head :title="artist.name" />
    <NavBar />

    <main>
        <Container>
            <PageHeader :title="artist.name" />
            <ItemList
                :items="artist.songs"
                :parent="{ slug: artist.slug }"
                showRouteName="artists.songs.show"
            />
        </Container>
    </main>
</template>

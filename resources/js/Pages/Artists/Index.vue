<script setup>
import Container from '@/Components/Container.vue';
import ItemList from '@/Components/ItemList.vue';
import LoadMoreButton from '@/Components/LoadMoreButton.vue';
import NavBar from '@/Components/NavBar.vue';
import PageHeader from '@/Components/PageHeader.vue';
import { ref } from 'vue';

const props = defineProps({
  artists: Object,
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
    loading.value = false;
  });
};
</script>

<template>
  <Head title="Artists" />
  <NavBar />

  <main>
    <Container>
      <PageHeader title="All Artists" />
      <ItemList showRouteName="artists.show" :items="artists" />

      <div class="mt-6 flex justify-center">
        <LoadMoreButton
          v-if="next_url"
          :onLoadMore="loadMoreArtists"
          :loading="loading"
        />
      </div>
    </Container>
  </main>
</template>

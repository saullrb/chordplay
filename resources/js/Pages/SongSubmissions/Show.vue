<script setup>
import Container from '@/Components/Container.vue';
import NavBar from '@/Components/NavBar.vue';
import PageHeader from '@/Components/PageHeader.vue';
import TextLink from '@/Components/TextLink.vue';
import { ref } from 'vue';
import SongSubmissionContent from './partials/SongSubmissionContent.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import IconLink from '@/Components/IconLink.vue';
import FormButtonIcon from './partials/FormButtonIcon.vue';

const props = defineProps({
    song_submission: Object,
});

const page = usePage();
const user = page.props.auth.user;
const song_key = ref(props.song_submission.key);
const is_dual_column = ref(false);

const approve_form = useForm();
const reject_form = useForm();

function approve() {
    approve_form.post(
        route('song_submissions.approve', { id: props.song_submission.id }),
    );
}

function reject() {
    reject_form.delete(
        route('song_submissions.destroy', { id: props.song_submission.id }),
    );
}
</script>

<template>
    <Head
        :title="`[Preview] ${song_submission.name} - ${song_submission.artist.name}`"
    />
    <NavBar />

    <Container>
        <header>
            <div
                class="mb-4 rounded bg-yellow-100 px-4 py-2 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200"
            >
                <strong>Preview:</strong> This is a preview of the song
                submission. It will look like this if approved.
            </div>
            <div class="flex items-center gap-2">
                <PageHeader :title="song_submission.name" />
                <FormButtonIcon :handleSubmit="approve">
                    <i class="fa-solid fa-check"></i>
                </FormButtonIcon>
                <IconLink
                    v-if="user"
                    :href="route('song_submissions.edit', { song_submission })"
                >
                    <i class="fa-solid fa-pencil"></i>
                </IconLink>
                <FormButtonIcon :handleSubmit="reject">
                    <i class="fa-solid fa-trash"></i>
                </FormButtonIcon>
            </div>
            <TextLink :href="route('artists.show', song_submission.artist)">
                {{ song_submission.artist.name }}
            </TextLink>
            <div class="my-6 flex flex-col gap-2">
                <div
                    class="flex items-center justify-start gap-2 text-sm dark:text-white"
                >
                    <span>Key: </span>
                    <span class="text-yellow-600">
                        {{ song_key }}
                    </span>
                </div>
            </div>
            <button
                @click="is_dual_column = !is_dual_column"
                class="cursor-pointer rounded-md border border-gray-300 bg-transparent px-3 py-1.5 text-sm text-gray-700 transition-colors duration-200 hover:bg-gray-300 hover:text-gray-900 sm:block dark:border-gray-700 dark:text-gray-300 dark:hover:bg-gray-800 dark:hover:text-white"
                :class="{
                    'border-gray-400 bg-yellow-600 text-white hover:bg-yellow-600 hover:text-white hover:brightness-105 dark:border-gray-600 dark:bg-yellow-600 dark:text-white dark:hover:bg-yellow-600 dark:hover:brightness-110':
                        is_dual_column,
                }"
            >
                <i class="fa-solid fa-table-columns mr-1"></i>
                Dual Column
            </button>
        </header>
        <main
            class="py-6 dark:text-white"
            :class="{
                'columns-2 gap-8': is_dual_column,
            }"
        >
            <SongSubmissionContent
                :original_key="song_submission.key"
                :content="song_submission.lines"
            />
        </main>
    </Container>
</template>

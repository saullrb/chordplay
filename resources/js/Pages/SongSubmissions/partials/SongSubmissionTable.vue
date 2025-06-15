<script setup>
import IconLink from '@/Components/IconLink.vue';

defineProps({
    submissions: Array,
});
</script>

<template>
    <table
        v-if="submissions.length"
        class="min-w-full divide-y-2 divide-gray-400 dark:divide-gray-700"
    >
        <thead class="sticky top-0 bg-gray-200 text-left dark:bg-gray-900">
            <tr class="*:font-bold *:text-gray-900 dark:*:text-white">
                <th class="px-3 py-2 whitespace-nowrap">Song</th>
                <th class="px-3 py-2 whitespace-nowrap">Artist</th>
                <th class="px-3 py-2 whitespace-nowrap">User</th>
                <th class="px-3 py-2 whitespace-nowrap"></th>
            </tr>
        </thead>

        <tbody
            class="divide-y divide-gray-500 *:even:bg-gray-300 dark:divide-gray-700 dark:*:even:bg-gray-950"
        >
            <tr
                class="*:text-gray-900 *:first:font-medium dark:*:text-white"
                v-for="submission in submissions"
            >
                <td class="px-3 py-2 whitespace-nowrap">
                    {{ submission.name }}
                </td>
                <td class="px-3 py-2 whitespace-nowrap">
                    {{ submission.artist.name }}
                </td>
                <td class="px-3 py-2 whitespace-nowrap">
                    {{ submission.user.name }}
                </td>
                <td class="flex justify-end px-3 py-2 whitespace-nowrap">
                    <IconLink :href="route('song_submissions.show', submission)"
                        ><i class="fa-solid fa-eye"></i
                    ></IconLink>
                </td>
            </tr>
        </tbody>
    </table>

    <p v-else class="px-3 py-2 dark:text-white">No Submissions Yet</p>
</template>

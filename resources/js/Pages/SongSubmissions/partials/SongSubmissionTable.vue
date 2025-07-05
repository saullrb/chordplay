<script setup>
import { EyeIconSolid } from '@/Components/UI/Icons';
import { Link } from '@inertiajs/vue3';

defineProps({
    submissions: {
        type: Array,
        required: true,
    },
    showUpdated: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <div class="border-base-content/10 overflow-x-auto rounded border">
        <table v-if="submissions.length" class="table">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Artist</th>
                    <th>User</th>
                    <th v-if="showUpdated">Updated At</th>
                    <th />
                </tr>
            </thead>

            <tbody>
                <tr
                    v-for="submission in submissions"
                    :key="submission.id"
                    class="hover:bg-primary/8"
                >
                    <td>
                        {{ submission.name }}
                    </td>
                    <td>
                        {{ submission.artist.name }}
                    </td>
                    <td>
                        {{ submission.user.name }}
                    </td>
                    <td v-if="showUpdated">
                        {{ submission.updated_at_human }}
                    </td>

                    <td>
                        <Link
                            class="btn btn-sm btn-soft btn-secondary btn-circle"
                            :href="route('song-submissions.show', submission)"
                        >
                            <EyeIconSolid class="size-5" />
                        </Link>
                    </td>
                </tr>
            </tbody>
        </table>
        <p v-else class="px-3 py-2">No Submissions Yet</p>
    </div>
</template>

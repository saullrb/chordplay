<script setup>
import ConfirmationDialog from '@/Components/UI/dialog/ConfirmationDialog.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const confirmDialog = ref();

const handleConfirmation = () => {
    confirmDialog.value.close();

    router.delete(route('profile.destroy'));
};
</script>

<template>
    <section class="space-y-6">
        <header>
            <h2 class="text-lg font-medium">Delete Account</h2>
        </header>

        <button class="btn btn-error" @click="confirmDialog.show()">
            Delete Account
        </button>

        <ConfirmationDialog
            ref="confirmDialog"
            title="Delete?"
            message="Are you sure you wan to delete your account?"
            @confirm="handleConfirmation"
        />
    </section>
</template>

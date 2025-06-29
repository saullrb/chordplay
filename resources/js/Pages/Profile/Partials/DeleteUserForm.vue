<script setup>
import DangerButton from '@/Components/UI/button/DangerButton.vue';
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

        <DangerButton @click="confirmDialog.show()">
            Delete Account
        </DangerButton>

        <ConfirmationDialog
            ref="confirmDialog"
            title="Delete?"
            message="Are you sure you wan to delete your account?"
            @confirm="handleConfirmation"
        />
    </section>
</template>

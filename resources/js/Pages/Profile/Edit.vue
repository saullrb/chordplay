<script setup>
import PageHeader from '@/Components/UI/PageHeader.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head } from '@inertiajs/vue3';
import DeleteUserForm from './Partials/DeleteUserForm.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
</script>

<template>
    <Head title="Profile" />

    <AppLayout>
        <template #header>
            <PageHeader title="Profile" />
        </template>

        <div class="overflow-hidden sm:rounded-lg">
            <div class="mt-6 space-y-6">
                <div class="bg-base-200 p-4 shadow-sm sm:rounded-lg sm:p-8">
                    <UpdateProfileInformationForm class="max-w-xl" />
                </div>

                <div class="bg-base-200 p-4 shadow-sm sm:rounded-lg sm:p-8">
                    <DeleteUserForm class="max-w-xl" />
                </div>
            </div>
        </div>
    </AppLayout>
</template>

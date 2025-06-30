<script setup>
import PageHeader from '@/Components/UI/PageHeader.vue';
import Panel from '@/Components/UI/Panel.vue';
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
                <Panel class="sm:p-8">
                    <UpdateProfileInformationForm class="max-w-xl" />
                </Panel>

                <Panel class="sm:p-8">
                    <DeleteUserForm class="max-w-xl" />
                </Panel>
            </div>
        </div>
    </AppLayout>
</template>

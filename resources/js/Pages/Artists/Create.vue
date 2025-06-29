<script setup>
import PageHeader from '@/Components/PageHeader.vue';
import PrimaryButton from '@/Components/UI/button/PrimaryButton.vue';
import InputError from '@/Components/UI/InputError.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    name: null,
});

function submit() {
    form.post(route('artists.store'));
}
</script>

<template>
    <Head title="Add Artist" />

    <AppLayout>
        <template #header>
            <PageHeader title="Add Artist" />
        </template>
        <div class="mt-12 flex items-center justify-center">
            <form
                class="flex w-full max-w-md flex-col"
                @submit.prevent="submit"
            >
                <fieldset
                    class="fieldset bg-base-200 rounded-box p-4 shadow-sm"
                >
                    <legend class="fieldset-legend">Artist's Name</legend>
                    <input
                        id="name"
                        v-model="form.name"
                        type="text"
                        class="input validator w-full"
                        placeholder="Name"
                        maxlength="100"
                        required
                        @input="form.errors.name = null"
                    />
                    <InputError :message="form.errors.name" />
                </fieldset>
                <div class="mt-4 flex justify-end">
                    <PrimaryButton type="submit"> Submit </PrimaryButton>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

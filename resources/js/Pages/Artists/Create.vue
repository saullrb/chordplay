<script setup>
import InputError from '@/Components/UI/InputError.vue';
import PageHeader from '@/Components/UI/PageHeader.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    name: null,
    profile_image: null,
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
                class="bg-base-200 rounded-box flex w-full max-w-md flex-col p-2 shadow"
                @submit.prevent="submit"
            >
                <fieldset class="fieldset p-4">
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
                <fieldset class="fieldset p-4">
                    <legend class="fieldset-legend">Profile Image</legend>
                    <input
                        type="file"
                        class="file-input"
                        @change="
                            (e) => (form.profile_image = e.target.files[0])
                        "
                    />
                    <label class="label">Max size 2MB</label>
                    <InputError :message="form.errors.profile_image" />
                </fieldset>
                <div class="mt-4 flex justify-end">
                    <button
                        class="btn btn-primary"
                        type="submit"
                        :disabled="form.processing"
                    >
                        Submit
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

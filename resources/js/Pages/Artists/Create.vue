<script setup>
import Container from '@/Components/Container.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import NavBar from '@/Components/NavBar.vue';
import PageHeader from '@/Components/PageHeader.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
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
    <NavBar />

    <main class="mt-6">
        <Container>
            <PageHeader title="Add Artist" />
            <div class="mt-12 flex items-center justify-center">
                <form
                    @submit.prevent="submit"
                    class="flex w-full max-w-md flex-col"
                >
                    <InputLabel for="name" value="Name" />
                    <input type="text" v-model="form.name" id="name" />
                    <InputError :message="form.errors.name" />
                    <div class="mt-4 flex justify-end">
                        <PrimaryButton
                            class="w-20"
                            :disabled="form.processing"
                            type="submit"
                        >
                            Submit
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Container>
    </main>
</template>

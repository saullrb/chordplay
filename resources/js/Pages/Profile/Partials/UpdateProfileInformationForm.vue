<script setup>
import InputError from '@/Components/UI/InputError.vue';
import { useForm, usePage } from '@inertiajs/vue3';

const user = usePage().props.auth.user;

const form = useForm({
    name: user.name,
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium">Profile Information</h2>

            <p class="text-base-content/70 mt-1 text-sm">
                Update your account's profile information
            </p>
        </header>

        <form
            class="mt-6 space-y-6"
            @submit.prevent="form.patch(route('profile.update'))"
        >
            <div>
                <form>
                    <label class="input validator">
                        <span class="label">Name</span>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            required
                            @input="form.errors.name = null"
                        />
                    </label>
                    <InputError class="mt-1" :message="form.errors.name" />
                </form>
            </div>

            <div class="flex items-center gap-4">
                <button class="btn btn-primary" :disabled="form.processing">
                    Save
                </button>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p
                        v-if="form.recentlySuccessful"
                        class="text-base-content/70 text-sm"
                    >
                        Saved.
                    </p>
                </Transition>
            </div>
        </form>
    </section>
</template>

<script setup>
import AppLayout from '@/Layouts/AppLayout.vue';
</script>

<template>
    <Head title="Login" />
    <AppLayout dusk="login-page">
        <div
            class="flex h-full flex-col items-center justify-start gap-6 pt-40"
        >
            <div class="w-full max-w-xs space-y-8 sm:max-w-sm md:max-w-md">
                <div class="text-center">
                    <h2
                        class="text-base-content text-2xl font-bold sm:text-3xl md:text-4xl"
                    >
                        Sign in to ChordPlay
                    </h2>
                    <p class="text-base-content/70 mt-2 text-sm sm:text-base">
                        Access your favorite songs and artists
                    </p>
                </div>
                <a
                    :href="route('google.redirect')"
                    class="btn btn-lg w-full border-gray-100 bg-white text-black hover:bg-gray-100"
                >
                    <svg
                        class="size-6"
                        aria-label="Google logo"
                        xmlns="http://www.w3.org/2000/svg"
                        viewBox="0 0 512 512"
                    >
                        <path
                            d="M153 292c30 82 118 95 171 60h62v48A192 192 0 0190 341"
                            fill="#34a853"
                        />
                        <path
                            d="m386 400a140 175 0 0053-179H260v74h102q-7 37-38 57"
                            fill="#4285f4"
                        />
                        <path
                            d="m90 341a208 200 0 010-171l63 49q-12 37 0 73"
                            fill="#fbbc02"
                        />
                        <path
                            d="m153 219c22-69 116-109 179-50l55-54c-78-75-230-72-297 55"
                            fill="#ea4335"
                        />
                    </svg>
                    Login with Google
                </a>
            </div>
        </div>
    </AppLayout>
</template>

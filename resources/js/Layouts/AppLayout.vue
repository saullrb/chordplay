<script setup>
import NavBar from '@/Components/Layout/NavBar.vue';
import FlashMessage from '@/Components/UI/FlashMessage.vue';
</script>

<template>
    <div dusk="page" class="flex min-h-screen flex-col">
        <NavBar />
        <FlashMessage />
        <div
            class="container mx-auto flex flex-1 flex-col px-4 sm:px-6 lg:px-8"
        >
            <header class="mt-6 w-full">
                <slot name="header" />
            </header>
            <main class="w-full pb-16">
                <slot />
            </main>
        </div>
    </div>
</template>

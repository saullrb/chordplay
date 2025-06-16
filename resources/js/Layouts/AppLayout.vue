<script setup>
import Container from '@/Components/Container.vue';
import NavBar from '@/Components/NavBar.vue';
</script>

<template>
    <div class="min-h-screen">
        <NavBar />

        <Container>
            <header class="mt-6">
                <slot name="header" />
            </header>
            <main class="mt-6 pb-6">
                <slot />
            </main>
        </Container>
    </div>
</template>

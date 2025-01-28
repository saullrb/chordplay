<script setup>
import { switchTheme } from '@/theme.js';
import { Link, router, usePage } from '@inertiajs/vue3';
import NavLink from '@/Components/NavLink.vue';
import Container from '@/Components/Container.vue';
import { computed, ref } from 'vue';

const show_dropdown = ref(false);
const is_menu_closed = ref(true);

const page = usePage();

const user = computed(() => page.props.auth.user);
</script>

<template>
    <nav class="bg-gray-200 text-black dark:bg-gray-900 dark:text-white">
        <Container class="container mx-auto w-full px-2 sm:px-6 lg:px-8">
            <div class="relative flex h-16 items-center justify-between">
                <div
                    class="absolute inset-y-0 left-0 flex items-center sm:hidden"
                >
                    <!-- Mobile menu button-->
                    <button
                        @click="is_menu_closed = !is_menu_closed"
                        type="button"
                        class="relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:ring-2 focus:ring-white focus:outline-hidden focus:ring-inset"
                        aria-controls="mobile-menu"
                        aria-expanded="false"
                    >
                        <span class="absolute -inset-0.5"></span>
                        <span class="sr-only">Open main menu</span>
                        <svg
                            v-show="is_menu_closed"
                            class="block size-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            aria-hidden="true"
                            data-slot="icon"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"
                            />
                        </svg>

                        <svg
                            v-show="!is_menu_closed"
                            class="block size-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke-width="1.5"
                            stroke="currentColor"
                            aria-hidden="true"
                            data-slot="icon"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M6 18 18 6M6 6l12 12"
                            />
                        </svg>
                    </button>
                </div>
                <div
                    class="flex flex-1 items-center justify-center sm:items-stretch sm:justify-start"
                >
                    <div class="flex shrink-0 items-center">
                        <Link
                            :href="route('home')"
                            class="text-md rounded-lg font-semibold text-yellow-600"
                        >
                            <div class="flex items-center gap-2">
                                <i class="fa-solid fa-music fa-lg"></i>
                                ChordPlay
                            </div>
                        </Link>
                    </div>
                    <div class="hidden sm:ml-6 sm:block">
                        <div class="flex space-x-4">
                            <NavLink
                                :href="route('home')"
                                :active="$page.component === 'Home'"
                                >Home</NavLink
                            >
                            <NavLink
                                :href="route('artists.index')"
                                :active="$page.component === 'Artists/Index'"
                                >Artists</NavLink
                            >
                        </div>
                    </div>
                </div>
                <div
                    class="absolute inset-y-0 right-0 flex items-center pr-2 sm:static sm:inset-auto sm:ml-6 sm:pr-0"
                >
                    <button
                        @click="switchTheme"
                        class="flex size-8 cursor-pointer items-center justify-center rounded-full text-gray-900 transition-colors hover:bg-gray-300 dark:text-white dark:hover:bg-gray-800"
                    >
                        <i class="fa-solid fa-circle-half-stroke"></i>
                    </button>

                    <div v-if="!user" class="flex">
                        <NavLink
                            :href="route('login')"
                            :active="$page.component === 'Auth/Login'"
                            >Log In</NavLink
                        >
                        <NavLink
                            :href="route('register')"
                            :active="$page.component === 'Auth/Register'"
                            >Register</NavLink
                        >
                    </div>
                    <!-- Profile dropdown -->
                    <div v-else class="relative ml-3">
                        <div>
                            <button
                                @click="show_dropdown = !show_dropdown"
                                type="button"
                                class="relative flex cursor-pointer rounded-full bg-gray-800 text-sm focus:ring-2 focus:ring-white focus:ring-offset-2 focus:ring-offset-gray-800 focus:outline-hidden"
                                id="user-menu-button"
                                aria-expanded="false"
                                aria-haspopup="true"
                            >
                                <span class="absolute -inset-1.5"></span>
                                <span class="sr-only">Open user menu</span>
                                <img
                                    class="size-8 rounded-full"
                                    src="https://images.unsplash.com/photo-1472099645785-5658abf4ff4e?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=2&w=256&h=256&q=80"
                                    alt=""
                                />
                            </button>
                        </div>

                        <Transition
                            enter-active-class="transition ease-out duration-100"
                            enter-from-class="transform opacity-0 scale-95"
                            enter-to-class="transform opacity-100 scale-100"
                            leave-active-class="transition ease-in duration-75"
                            leave-from-class="transform opacity-100 scale-100"
                            leave-to-class="transform opacity-0 scale-95"
                        >
                            <div
                                v-show="show_dropdown"
                                @click="show_dropdown = false"
                                class="absolute right-0 z-10 mt-2 w-48 origin-top-right rounded-md bg-gray-200 py-1 text-gray-900 ring-1 shadow-lg ring-black/5 focus:outline-hidden dark:bg-gray-900 dark:text-white dark:ring-white/5"
                                role="menu"
                                aria-orientation="vertical"
                                aria-labelledby="user-menu-button"
                                tabindex="-1"
                            >
                                <NavLink
                                    :active="route().current('profile.edit')"
                                    :href="route('profile.edit')"
                                    >Your Profile</NavLink
                                >
                                <NavLink
                                    :active="route().current('dashboard')"
                                    :href="route('dashboard')"
                                    >Dashboard</NavLink
                                >
                                <button
                                    class="block w-full rounded-lg px-4 py-2 text-start text-sm font-semibold text-gray-500 hover:bg-gray-500 hover:text-white dark:text-gray-400 dark:hover:bg-gray-800"
                                    @click="router.post(route('logout'))"
                                >
                                    Log Out
                                </button>
                            </div>
                        </Transition>
                    </div>
                </div>
            </div>
        </Container>

        <!-- Mobile menu, show/hide based on menu state. -->
        <div v-if="!is_menu_closed" class="sm:hidden" id="mobile-menu">
            <div class="space-y-1 px-2 pt-2 pb-3">
                <NavLink
                    :href="route('home')"
                    :active="$page.component === 'Home'"
                    >Home</NavLink
                >
                <NavLink
                    :href="route('artists.index')"
                    :active="$page.component === 'Artists/Index'"
                    >Artists</NavLink
                >
            </div>
        </div>
    </nav>
</template>

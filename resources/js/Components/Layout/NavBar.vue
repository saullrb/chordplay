<script setup>
import Logo from '@/Components/Logo.vue';
import NavLink from '@/Components/NavLink.vue';
import ThemeSelector from '@/Components/ThemeSelector.vue';
import Dropdown from '@/Components/UI/Dropdown.vue';
import { MenuIcon } from '@/Components/UI/Icons';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const page = usePage();

const user = computed(() => page.props.auth.user);
</script>

<template>
    <nav class="navbar bg-base-100 sticky top-0 z-50 shadow-sm">
        <div class="navbar-start">
            <Dropdown class="md:hidden">
                <template #trigger>
                    <MenuIcon class="size-5" />
                </template>

                <li>
                    <NavLink route-name="home"> Home </NavLink>
                </li>
                <li>
                    <NavLink route-name="artists.index"> Artists </NavLink>
                </li>
            </Dropdown>
            <Logo class="mr-2" />
            <ul class="menu menu-horizontal hidden gap-2 px-1 md:flex">
                <li>
                    <NavLink route-name="home"> Home </NavLink>
                </li>
                <li>
                    <NavLink route-name="artists.index"> Artists </NavLink>
                </li>
            </ul>
        </div>
        <div class="navbar-end">
            <ul class="menu menu-horizontal items-center gap-3">
                <li>
                    <ThemeSelector />
                </li>

                <li>
                    <Dropdown
                        v-if="user"
                        class="dropdown-end"
                        trigger-class="avatar btn-circle"
                    >
                        <template #trigger>
                            <div class="w-8 rounded-full">
                                <img alt="Avatar" src="/images/avatar.webp" />
                            </div>
                        </template>

                        <li>
                            <NavLink route-name="dashboard">
                                Dashboard
                            </NavLink>
                        </li>
                        <li>
                            <NavLink route-name="profile.edit">
                                Profile
                            </NavLink>
                        </li>
                        <li class="mt-4">
                            <NavLink route-name="logout" method="post">
                                Log Out
                            </NavLink>
                        </li>
                    </Dropdown>
                    <NavLink v-else route-name="login"> Login </NavLink>
                </li>
            </ul>
        </div>
    </nav>
</template>

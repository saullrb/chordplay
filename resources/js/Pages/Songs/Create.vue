<script setup>
import Container from '@/Components/Container.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import NavBar from '@/Components/NavBar.vue';
import PageHeader from '@/Components/PageHeader.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useForm } from '@inertiajs/vue3';
import { nextTick } from 'vue';

const form = useForm({
    name: null,
    key: null,
    sections: [
        {
            is_lyrical: true,
            order: 1,
            lyric_lines: [
                {
                    order: 1,
                    text: null,
                    chords_placements: [
                        {
                            chord: null,
                            position: 0,
                        },
                    ],
                },
            ],
            chords_sequence: [
                {
                    order: 1,
                    chords: [{ name: null }],
                },
            ],
        },
    ],
});

const props = defineProps({
    song_keys: Array,
    artist: Object,
});

function removeSection(section_index) {
    form.sections.splice(section_index, 1);

    form.sections.forEach((section, index) => {
        section.order = index + 1;
    });
}

function addLine(section_index) {
    const section = form.sections[section_index];
    section.lyric_lines.push({
        order: section.lyric_lines.length + 1,
        text: null,
        chords_placements: [
            {
                chord: null,
                position: 0,
            },
        ],
    });

    nextTick(() => {
        document
            .querySelector(
                `#section-${section_index}-line-${section.lyric_lines.length - 1}`,
            )
            .focus();
    });
}

function removeLine(section_index, line_index) {
    const section = form.sections[section_index];
    section.lyric_lines.splice(line_index, 1);

    section.lyric_lines.forEach((line, index) => {
        line.order = index + 1;
    });
}

function submit() {
    form.post(route('artists.songs.store', props.artist));
}
</script>

<template>
    <Head title="Add Song" />
    <NavBar />

    <main class="mt-6">
        <Container>
            <PageHeader title="Add Song" />
            <div class="mt-12 flex justify-between gap-12">
                <form
                    @submit.prevent="submit"
                    class="flex w-full flex-col gap-2"
                >
                    <div class="flex flex-col gap-1">
                        <InputLabel for="key" value="Key" />
                        <select v-model="form.key" id="key">
                            <option
                                :key="key"
                                v-for="key in song_keys"
                                :value="key"
                            >
                                {{ key }}
                            </option>
                        </select>
                        <InputError :message="form.errors.key" />
                    </div>
                    <div class="flex flex-col gap-1">
                        <InputLabel for="name" value="Name" />
                        <input type="text" v-model="form.name" id="name" />
                        <InputError :message="form.errors.name" />
                    </div>

                    <!-- SECTIONS -->
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold">Sections</h3>
                        <div class="mt-4 space-y-6">
                            <div
                                v-for="(
                                    section, section_index
                                ) in form.sections"
                                :key="section_index"
                                :id="`section-${section_index}`"
                            >
                                <div
                                    class="mb-4 flex items-center justify-between"
                                >
                                    <div class="flex items-center gap-4">
                                        <span class="font-semibold"
                                            >Section {{ section.order }}</span
                                        >
                                        <div class="flex items-center gap-2">
                                            <InputLabel
                                                :for="
                                                    'is_lyrical' + section_index
                                                "
                                                value="Is Lyrical?"
                                            />
                                            <input
                                                type="checkbox"
                                                v-model="section.is_lyrical"
                                                :id="
                                                    'is_lyrical' + section_index
                                                "
                                            />
                                        </div>
                                    </div>
                                    <button
                                        @click="removeSection(section_index)"
                                        class="text-red-500"
                                    >
                                        Remove Section
                                    </button>
                                </div>

                                <!-- Lyrical Section -->
                                <div v-if="section.is_lyrical" class="mb-4">
                                    <h4 class="mb-2 font-medium">Lyrics</h4>
                                    <div class="space-y-4">
                                        <div
                                            v-for="(
                                                line, line_index
                                            ) in section.lyric_lines"
                                            :key="line_index"
                                        >
                                            <div
                                                class="mb-2 flex items-center justify-between"
                                            >
                                                <span
                                                    >Line {{ line.order }}</span
                                                >

                                                <button
                                                    class="text-red-500"
                                                    @click="
                                                        removeLine(
                                                            section_index,
                                                            line_index,
                                                        )
                                                    "
                                                >
                                                    <i
                                                        class="fa-solid fa-multiply"
                                                    ></i>
                                                </button>
                                            </div>

                                            <input
                                                @keypress.prevent.enter="
                                                    addLine(section_index)
                                                "
                                                :id="`section-${section_index}-line-${line_index}`"
                                                type="text"
                                                v-model="line.text"
                                                class="mb-2 w-full p-2"
                                                placeholder="Press ENTER to create a new line"
                                            />
                                        </div>
                                    </div>
                                </div>

                                <div v-else class="mb-4">
                                    <h4 class="mb-2 font-medium">Chords</h4>
                                    <div class="space-y-2">
                                        <div
                                            v-for="(
                                                sequence, sequence_index
                                            ) in section.chords_sequence"
                                            :key="sequence_index"
                                            class="flex items-center gap-2"
                                        >
                                            <span>{{ sequence.order }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

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

                <div class="w-full">Lyrics and Chords Here</div>
            </div>
        </Container>
    </main>
</template>

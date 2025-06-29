<script setup>
import Dropdown from '@/Components/UI/Dropdown.vue';
import {
    MinusIcon,
    MultiColumnIcon,
    PlusIconSolid,
} from '@/Components/UI/Icons';
import { ref, watch } from 'vue';

const props = defineProps({
    song_key: { type: String, required: true },
    available_keys: Array,
    show_capo_options: Boolean,
    show_key_change_buttons: Boolean,
});

const song_key = ref(props.song_key);
const capo_position = ref(0);
const multi_column = ref(false);
const key_offset = ref(0);

defineExpose({
    key_offset,
    multi_column,
});

watch(capo_position, (new_position, old_position) => {
    new_position - old_position > 0
        ? transpose('down', new_position - old_position, false)
        : transpose('up', Math.abs(new_position - old_position), false);
});

function selectCapoPostion(position) {
    capo_position.value = position;
}

function transpose(direction, half_steps, should_change_key = true) {
    let key_index = props.available_keys.findIndex(
        (key) => key === song_key.value,
    );

    if (direction === 'down') {
        key_index =
            (key_index - half_steps + props.available_keys.length) %
            props.available_keys.length;
        key_offset.value -= half_steps;
    } else {
        key_index = (key_index + half_steps) % props.available_keys.length;
        key_offset.value += half_steps;
    }

    if (should_change_key) {
        song_key.value = props.available_keys[key_index];
    }
}
</script>

<template>
    <div>
        <div class="my-6 flex flex-col gap-4">
            <div class="flex items-center justify-start gap-2">
                <span>Key: </span>
                <div class="flex items-center justify-between">
                    <button
                        v-if="show_key_change_buttons"
                        dusk="transpose-down-button"
                        @click="() => transpose('down', 1)"
                        class="btn btn-xs btn-circle btn-soft"
                        aria-label="Transpose down"
                    >
                        <MinusIcon class="size-4" />
                    </button>
                    <span dusk="song-key" class="text-accent w-10 text-center">
                        {{ song_key }}
                    </span>
                    <button
                        v-if="show_key_change_buttons"
                        dusk="transpose-up-button"
                        @click="() => transpose('up', 1)"
                        class="btn btn-xs btn-circle btn-soft"
                        aria-label="Transpose up"
                    >
                        <PlusIconSolid class="size-4" />
                    </button>
                </div>
            </div>

            <div
                v-if="show_capo_options"
                class="flex items-center justify-start gap-2"
            >
                <label for="capo">Capo Fret:</label>
                <Dropdown
                    class="dropdown-right"
                    :trigger-class="{
                        'btn-circle': true,
                        'btn-accent': capo_position !== 0,
                    }"
                    dusk="capo-dropdown"
                >
                    <template #trigger> {{ capo_position }} </template>
                    <li
                        v-for="(_, n) in 12"
                        :key="n"
                        @click="() => selectCapoPostion(n)"
                    >
                        <button
                            class="btn btn-xs btn-accent btn-ghost"
                            :class="{
                                'btn-active': n === capo_position,
                            }"
                            :dusk="'capo-' + n"
                        >
                            {{ n }}
                        </button>
                    </li>
                </Dropdown>
            </div>
        </div>
        <button
            @click="multi_column = !multi_column"
            class="btn btn-sm hidden lg:flex"
            :class="{
                'btn-success': multi_column,
            }"
        >
            <MultiColumnIcon class="size-5" />
            Multi Column
        </button>
    </div>
</template>

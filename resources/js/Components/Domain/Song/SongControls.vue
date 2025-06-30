<script setup>
import Dropdown from '@/Components/UI/Dropdown.vue';
import {
    MinusIcon,
    MultiColumnIcon,
    PlusIconSolid,
} from '@/Components/UI/Icons';
import { ref, watch } from 'vue';

const props = defineProps({
    songKey: {
        type: String,
        required: true,
    },
    availableKeys: {
        type: Array,
        default: () => [],
    },
    showCapoOptions: Boolean,
    showKeyChangeButtons: Boolean,
});

const songKey = ref(props.songKey);
const capoPosition = ref(0);
const multiColumn = ref(false);
const keyOffset = ref(0);

defineExpose({
    keyOffset: keyOffset,
    multiColumn: multiColumn,
});

watch(capoPosition, (newPosition, oldPosition) => {
    newPosition - oldPosition > 0
        ? transpose('down', newPosition - oldPosition, false)
        : transpose('up', Math.abs(newPosition - oldPosition), false);
});

function selectCapoPostion(position) {
    capoPosition.value = position;
}

function transpose(direction, halfSteps, shouldChangeKey = true) {
    let keyIndex = props.availableKeys.findIndex(
        (key) => key === songKey.value,
    );

    if (direction === 'down') {
        keyIndex =
            (keyIndex - halfSteps + props.availableKeys.length) %
            props.availableKeys.length;
        keyOffset.value -= halfSteps;
    } else {
        keyIndex = (keyIndex + halfSteps) % props.availableKeys.length;
        keyOffset.value += halfSteps;
    }

    if (shouldChangeKey) {
        songKey.value = props.availableKeys[keyIndex];
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
                        v-if="showKeyChangeButtons"
                        dusk="transpose-down-button"
                        class="btn btn-xs btn-circle btn-soft"
                        aria-label="Transpose down"
                        @click="() => transpose('down', 1)"
                    >
                        <MinusIcon class="size-4" />
                    </button>
                    <span dusk="song-key" class="text-accent w-10 text-center">
                        {{ songKey }}
                    </span>
                    <button
                        v-if="showKeyChangeButtons"
                        dusk="transpose-up-button"
                        class="btn btn-xs btn-circle btn-soft"
                        aria-label="Transpose up"
                        @click="() => transpose('up', 1)"
                    >
                        <PlusIconSolid class="size-4" />
                    </button>
                </div>
            </div>

            <div
                v-if="showCapoOptions"
                class="flex items-center justify-start gap-2"
            >
                <label for="capo">Capo Fret:</label>
                <Dropdown
                    class="dropdown-right"
                    :trigger-class="{
                        'btn-circle': true,
                        'btn-accent': capoPosition !== 0,
                    }"
                    dusk="capo-dropdown"
                >
                    <template #trigger>
                        {{ capoPosition }}
                    </template>
                    <li
                        v-for="(_, n) in 12"
                        :key="n"
                        @click="() => selectCapoPostion(n)"
                    >
                        <button
                            class="btn btn-xs btn-accent btn-ghost"
                            :class="{
                                'btn-active': n === capoPosition,
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
            class="btn btn-sm hidden lg:flex"
            :class="{
                'btn-success': multiColumn,
            }"
            @click="multiColumn = !multiColumn"
        >
            <MultiColumnIcon class="size-5" />
            Multi Column
        </button>
    </div>
</template>

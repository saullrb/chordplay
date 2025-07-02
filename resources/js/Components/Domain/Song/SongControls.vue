<script setup>
import Dropdown from '@/Components/UI/Dropdown.vue';
import {
    MinusIcon,
    MultiColumnIcon,
    PlusIconSolid,
} from '@/Components/UI/Icons';
import { ref, watch } from 'vue';

const NUM_KEYS = 12;

const props = defineProps({
    originalSongKey: {
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

const originalKeyIndex = props.availableKeys.findIndex(
    (key) => key === props.originalSongKey,
);
const currentSongKey = ref(props.originalSongKey);
const capoPosition = ref(0);
const keyOffset = ref(0);
const multiColumn = ref(false);

defineExpose({
    keyOffset: keyOffset,
    multiColumn: multiColumn,
});

watch(capoPosition, (newPosition, oldPosition) => {
    if (newPosition > oldPosition) {
        transposeDown(newPosition - oldPosition);
    } else {
        transposeUp(oldPosition - newPosition);
    }
});

function selectCapoPosition(position) {
    capoPosition.value = position;
}

function transposeUp(halfSteps = 1) {
    keyOffset.value = (keyOffset.value + halfSteps) % NUM_KEYS;
    setSongKey();
}

function transposeDown(halfSteps = 1) {
    keyOffset.value = (keyOffset.value - halfSteps) % NUM_KEYS;
    setSongKey();
}

function setSongKey() {
    let currentKeyIndex =
        (originalKeyIndex + keyOffset.value + NUM_KEYS) % NUM_KEYS;

    currentSongKey.value = props.availableKeys[currentKeyIndex];
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
                        @click="() => transposeDown()"
                    >
                        <MinusIcon class="size-4" />
                    </button>
                    <span dusk="song-key" class="text-accent w-10 text-center">
                        {{ currentSongKey }}
                    </span>
                    <button
                        v-if="showKeyChangeButtons"
                        dusk="transpose-up-button"
                        class="btn btn-xs btn-circle btn-soft"
                        aria-label="Transpose up"
                        @click="() => transposeUp()"
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
                        @click="() => selectCapoPosition(n)"
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

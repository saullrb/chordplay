<script setup>
import InputError from '@/Components/UI/InputError.vue';
import { reactive } from 'vue';

const props = defineProps({
    availableKeys: {
        type: Array,
        default: () => [],
    },
    initialData: {
        type: Object,
        default: () => ({
            name: '',
            key: null,
            content: '',
        }),
    },
    submitLabel: {
        type: String,
        default: 'Submit',
    },
});

const emit = defineEmits(['submit']);

const form = reactive(props.initialData);

function handleSubmit() {
    emit('submit');
}
</script>
<template>
    <form class="flex w-full flex-col gap-2" @submit.prevent="handleSubmit">
        <div class="flex flex-col gap-1">
            <label class="floating-label input validator w-full">
                <span>Name</span>
                <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    placeholder="Name"
                    dusk="song-name-input"
                    required
                    maxlength="100"
                    @input="form.errors.name = null"
                />
            </label>
            <InputError :message="form.errors?.name" />
        </div>
        <div class="flex flex-col gap-1">
            <label class="select validator w-full">
                <span class="label">Key</span>
                <select
                    id="key"
                    v-model="form.key"
                    dusk="song-key-select"
                    required
                    @input="form.errors.key = null"
                >
                    <option
                        v-for="key in availableKeys"
                        :key="key"
                        :value="key"
                    >
                        {{ key }}
                    </option>
                </select>
            </label>
            <InputError :message="form.errors?.key" />
        </div>
        <div class="flex flex-col gap-1">
            <textarea
                id="content"
                v-model="form.content"
                :placeholder="'Use [] to denote chords. Example -> [C]   [Dm]   [F7].\r\nIf the first non-empty character of the line is not [ , then it will be considered a lyric line.'"
                class="textarea validator h-80 w-full"
                dusk="song-content-textarea"
                required
                @input="form.errors.content = null"
            />
        </div>
        <InputError dusk="content-errors" :message="form.errors?.content" />
        <div class="mt-4 flex justify-end gap-2">
            <button
                class="btn btn-primary"
                dusk="submit-button"
                :disabled="form.processing"
                type="submit"
            >
                {{ submitLabel }}
            </button>
        </div>
    </form>
</template>

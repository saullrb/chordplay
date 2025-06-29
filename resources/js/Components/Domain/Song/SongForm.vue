<script setup>
import InputError from '@/Components/UI/InputError.vue';
import PrimaryButton from '@/Components/UI/button/PrimaryButton.vue';
import { reactive } from 'vue';

const props = defineProps({
    available_keys: Array,
    initial_data: {
        type: Object,
        default: () => ({
            name: '',
            key: null,
            content: '',
        }),
    },
    submit_label: {
        type: String,
        default: 'Submit',
    },
});

const emit = defineEmits(['submit']);

const form = reactive(props.initial_data);

function handleSubmit() {
    emit('submit');
}
</script>
<template>
    <form @submit.prevent="handleSubmit" class="flex w-full flex-col gap-2">
        <div class="flex flex-col gap-1">
            <label class="floating-label input validator w-full">
                <span>Name</span>
                <input
                    v-model="form.name"
                    id="name"
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
                    v-model="form.key"
                    id="key"
                    dusk="song-key-select"
                    required
                    @input="form.errors.key = null"
                >
                    <option
                        :key="key"
                        v-for="key in available_keys"
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
                v-model="form.content"
                id="content"
                :placeholder="'Use [] to denote chords. Example -> [C]   [Dm]   [F7].\r\nIf the first non-empty character of the line is not [ , then it will be considered a lyric line.'"
                class="textarea validator h-80 w-full"
                dusk="song-content-textarea"
                required
                @input="form.errors.content = null"
            ></textarea>
        </div>
        <InputError dusk="content-errors" :message="form.errors?.content" />
        <div class="mt-4 flex justify-end gap-2">
            <PrimaryButton
                dusk="submit-button"
                :disabled="form.processing"
                type="submit"
            >
                {{ submit_label }}
            </PrimaryButton>
        </div>
    </form>
</template>

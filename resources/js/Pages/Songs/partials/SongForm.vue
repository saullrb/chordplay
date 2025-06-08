<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
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
            <InputLabel for="name" value="Name" />
            <input class="px-2 py-1 dark:bg-gray-800 dark:text-white" type="text" required v-model="form.name"
                id="name" />
            <InputError :message="form.errors?.name" />
        </div>
        <div class="flex flex-col gap-1">
            <InputLabel for="key" value="Key" />
            <select class="bg-white p-2 dark:bg-gray-800 dark:text-white" v-model="form.key" id="key">
                <option :key="key" v-for="key in available_keys" :value="key"
                    class="px-2 dark:bg-gray-800 dark:text-white">
                    {{ key }}
                </option>
            </select>
            <InputError :message="form.errors?.key" />
        </div>
        <div class="flex flex-col gap-1">
            <InputLabel for="content" value="Content" />
            <textarea v-model="form.content" id="content" required
                :placeholder="'Use [] to denote chords. Example -> [C]   [Dm]   [F7].\r\nIf the first non-empty character of the line is not [ , then it will be considered a lyric line.'"
                class="h-80 resize-y overflow-auto px-2 font-mono text-sm leading-5 tracking-tighter whitespace-pre-wrap dark:bg-gray-800 dark:text-white"></textarea>
        </div>
        <InputError :message="form.errors?.content" />
        <div class="mt-4 flex justify-end gap-2">
            <PrimaryButton :disabled="form.processing" type="submit">
                {{ submit_label }}
            </PrimaryButton>
        </div>
    </form>
</template>

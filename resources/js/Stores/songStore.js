import { defineStore } from 'pinia';
import { reactive, ref } from 'vue';

export const useSongStore = defineStore('song', () => {
    const originalSongKey = ref(null);
    const currentSongKey = ref(null);
    const availableKeys = ref([]);
    const chords = reactive({});
    const missingChords = ref(new Set());
    const capoPosition = ref(0);
    const keyOffset = ref(0);

    function init({ key, initialChords = [], availableKeysArray = [] }) {
        originalSongKey.value = key;
        currentSongKey.value = key;
        availableKeys.value = availableKeysArray;
        Object.assign(chords, initialChords);
        missingChords.value.clear();
        capoPosition.value = 0;
        keyOffset.value = 0;
    }

    function addChords(newChords) {
        Object.assign(chords, newChords);
    }

    function clearMissingChords() {
        missingChords.value.clear();
    }

    function addMissingChord(name) {
        if (!(name in chords)) {
            chords[name] = { shapes: [], defaultShapeId: null };
        }
        missingChords.value.add(name);
    }

    return {
        originalSongKey,
        currentSongKey,
        availableKeys,
        chords,
        missingChords,
        capoPosition,
        keyOffset,
        init,
        addChords,
        clearMissingChords,
        addMissingChord,
    };
});

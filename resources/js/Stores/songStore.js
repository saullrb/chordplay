import { ref } from 'vue';

const originalSongKey = ref(null);
const currentSongKey = ref(null);
const availableKeys = ref([]);
const chords = ref({});
const missingChords = ref(new Set());

const capoPosition = ref(0);
const keyOffset = ref(0);

export function initSongStore({ key, initialChords, availableKeysArray }) {
    originalSongKey.value = key;
    currentSongKey.value = key;
    availableKeys.value = availableKeysArray;
    chords.value = initialChords;
}

export function getOriginalSongKeyRef() {
    return originalSongKey;
}

export function getCurrentSongKeyRef() {
    return currentSongKey;
}

export function setCurrentSongKey(key) {
    currentSongKey.value = key;
}

export function getChordsRef() {
    return chords;
}

export function addChords(newChords) {
    Object.assign(chords.value, newChords);
}

export function getKeyOffsetRef() {
    return keyOffset;
}

export function setKeyOffset(offset) {
    keyOffset.value = offset;
}

export function getAvailableKeysRef() {
    return availableKeys;
}

export function getCapoPositionRef() {
    return capoPosition;
}

export function setCapoPosition(position) {
    capoPosition.value = position;
}

export function getMissingChordsRef() {
    return missingChords;
}

export function addMissingChord(chord) {
    missingChords.value.add(chord);
}

export function clearMissingChords() {
    missingChords.value.clear();
}

import { ref } from 'vue';

const originalSongKey = ref(null);
const currentSongKey = ref(null);
const availableKeys = ref([]);

const capoPosition = ref(0);
const keyOffset = ref(0);

export function initSongStore({ key, availableKeysArray }) {
    originalSongKey.value = key;
    currentSongKey.value = key;
    availableKeys.value = availableKeysArray;
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

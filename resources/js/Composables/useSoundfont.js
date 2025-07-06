import { Reverb, Soundfont } from 'smplr';

let context = null;
let instrument = null;
let reverb = null;

export function useSoundfont() {
    async function init() {
        if (!context) {
            context = new AudioContext();
            instrument = await new Soundfont(context, {
                instrument: 'acoustic_guitar_steel',
                kit: 'FluidR3_GM',
            }).load;
            reverb = new Reverb(context);
        }
    }

    async function playChord(notes) {
        await init();
        const strumDelay = 0.08;
        const now = context.currentTime;

        instrument.output.addEffect('reverb', reverb, 0.5);

        notes.forEach((note, index) => {
            const time = now + index * strumDelay;
            instrument.start({ note, time, duration: 1 });
        });
    }

    return { playChord };
}

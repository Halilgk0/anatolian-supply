import { clamp, onFrame, onMeasure, reducedMotion } from './motion';

/**
 * Bands only move while the page scrolls: down pushes one way, up the other,
 * and they coast to a stop when scrolling ends.
 */
export function initMarquee() {
    const rows = [...document.querySelectorAll('[data-marquee]')].map((row) => ({
        row,
        direction: Number(row.dataset.marquee) || 1,
        offset: 0,
        width: 0,
    }));

    if (reducedMotion || rows.length === 0) {
        return;
    }

    let speed = 0;
    let push = 0;
    let running = false;

    onMeasure(() => {
        rows.forEach((item) => {
            item.width = item.row.firstElementChild.getBoundingClientRect().width;
        });
    });

    const tick = () => {
        speed += (push - speed) * 0.16;
        push *= 0.86;

        rows.forEach((item) => {
            if (!item.width) {
                return;
            }

            item.offset = (((item.offset + speed * item.direction) % item.width) + item.width) % item.width;
            item.row.style.transform = `translate3d(${(-item.offset).toFixed(1)}px, 0, 0)`;
        });

        if (Math.abs(speed) < 0.03 && Math.abs(push) < 0.03) {
            running = false;

            return;
        }

        requestAnimationFrame(tick);
    };

    onFrame(({ velocity }) => {
        push = clamp(velocity * 0.9, -45, 45);

        if (!running) {
            running = true;
            requestAnimationFrame(tick);
        }
    });
}

import { clamp, onFrame, onMeasure, pageTop, reducedMotion } from './motion';

/**
 * Pins the product section and converts vertical scroll into sideways travel along the rod.
 * Each hanging tag swings on its string when the rod moves, then settles.
 */
export function initRail() {
    const section = document.querySelector('[data-rail]');

    if (!section || reducedMotion) {
        return;
    }

    const sticky = section.querySelector('[data-rail-sticky]');
    const track = section.querySelector('[data-rail-track]');
    const counter = section.querySelector('[data-rail-count]');
    const bar = section.querySelector('[data-rail-bar]');
    const hangers = [...track.querySelectorAll('[data-hanger]')];
    const swings = hangers.map((element, index) => ({
        element,
        angle: 0,
        velocity: 0,
        stiffness: 0.045 + index * 0.007,
    }));

    let top = 0;
    let distance = 0;
    let lastX = 0;
    let push = 0;
    let swinging = false;

    section.classList.add('is-pinned');

    onMeasure(() => {
        section.style.height = '';
        track.style.transform = '';

        const last = track.lastElementChild;
        const padding = parseFloat(getComputedStyle(track).paddingLeft);

        distance = Math.max(0, last.offsetLeft + last.offsetWidth + padding - sticky.clientWidth);
        section.style.height = `${sticky.offsetHeight + distance}px`;
        top = pageTop(section);
        lastX = -clamp((window.scrollY - top) / (distance || 1)) * distance;
    });

    const swing = () => {
        let moving = false;

        swings.forEach((item) => {
            item.velocity += push * 0.045 - item.angle * item.stiffness;
            item.velocity *= 0.9;
            item.angle = clamp(item.angle + item.velocity, -14, 14);
            item.element.style.transform = `rotate(${item.angle.toFixed(3)}deg)`;

            if (Math.abs(item.angle) > 0.02 || Math.abs(item.velocity) > 0.02) {
                moving = true;
            }
        });

        push = 0;

        if (moving) {
            requestAnimationFrame(swing);
        } else {
            swinging = false;
        }
    };

    onFrame(({ y }) => {
        const progress = distance ? clamp((y - top) / distance) : 0;
        const x = -progress * distance;

        track.style.transform = `translate3d(${x.toFixed(1)}px, 0, 0)`;
        bar.style.transform = `scaleX(${progress.toFixed(4)})`;
        counter.textContent = String(Math.round(progress * (hangers.length - 1)) + 1);

        push += x - lastX;
        lastX = x;

        if (!swinging && push !== 0) {
            swinging = true;
            requestAnimationFrame(swing);
        }
    });

    // Keyboard users: bring a focused tag into view by scrolling the page, not the clipped track.
    track.addEventListener('focusin', (event) => {
        const hanger = event.target.closest('[data-hanger]');

        sticky.scrollLeft = 0;
        track.scrollLeft = 0;

        if (!hanger || !distance) {
            return;
        }

        const target = clamp(hanger.offsetLeft + hanger.offsetWidth / 2 - sticky.clientWidth / 2, 0, distance);

        window.scrollTo({ top: top + target, behavior: 'smooth' });
    });
}

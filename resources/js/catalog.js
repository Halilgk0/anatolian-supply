import { clamp, onFrame, reducedMotion } from './motion';

/**
 * Tags hanging on the catalog pegboard sway a little while the page scrolls and settle when it stops.
 * Neighbours swing in opposite directions and at slightly different speeds so the board looks alive.
 */
export function initPegboard() {
    const tags = [...document.querySelectorAll('[data-swing]')];

    if (reducedMotion || tags.length === 0) {
        return;
    }

    const swings = tags.map((element, index) => ({
        element,
        angle: 0,
        velocity: 0,
        direction: index % 2 === 0 ? 1 : -1,
        stiffness: 0.05 + (index % 3) * 0.009,
    }));

    let push = 0;
    let running = false;

    const tick = () => {
        let moving = false;

        swings.forEach((item) => {
            item.velocity += push * 0.014 * item.direction - item.angle * item.stiffness;
            item.velocity *= 0.9;
            item.angle = clamp(item.angle + item.velocity, -6, 6);
            item.element.style.transform = `rotate(${item.angle.toFixed(3)}deg)`;

            if (Math.abs(item.angle) > 0.02 || Math.abs(item.velocity) > 0.02) {
                moving = true;
            }
        });

        push = 0;

        if (moving) {
            requestAnimationFrame(tick);
        } else {
            running = false;
        }
    };

    onFrame(({ velocity }) => {
        push += clamp(velocity, -60, 60);

        if (!running && push !== 0) {
            running = true;
            requestAnimationFrame(tick);
        }
    });
}

/**
 * On narrow screens the category row scrolls sideways; start it at the selected category.
 */
export function initCategoryNav() {
    const nav = document.querySelector('[data-category-nav]');
    const active = nav?.querySelector('[aria-current="page"]');

    if (!active) {
        return;
    }

    const offset = active.offsetLeft - (nav.clientWidth - active.offsetWidth) / 2;

    nav.scrollLeft = Math.max(0, offset);
}

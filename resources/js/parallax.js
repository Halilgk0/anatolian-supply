import { clamp, onFrame, onMeasure, reducedMotion } from './motion';

/**
 * Moves [data-parallax] elements relative to their parent while it passes through the viewport.
 *
 * data-parallax     vertical px per viewport of scroll
 * data-parallax-x   horizontal px per viewport of scroll
 * data-rotate       degrees per viewport of scroll
 * data-fade         opacity lost per viewport once scrolled past
 */
export function initParallax() {
    const elements = [...document.querySelectorAll('[data-parallax]')];

    if (reducedMotion || elements.length === 0) {
        return;
    }

    let items = [];

    onMeasure(() => {
        items = elements.map((element) => {
            const rect = element.parentElement.getBoundingClientRect();

            return {
                element,
                center: rect.top + window.scrollY + rect.height / 2,
                y: Number(element.dataset.parallax) || 0,
                x: Number(element.dataset.parallaxX) || 0,
                rotate: Number(element.dataset.rotate) || 0,
                fade: Number(element.dataset.fade) || 0,
            };
        });
    });

    onFrame(({ y, height }) => {
        for (const item of items) {
            const progress = (y + height / 2 - item.center) / height;

            if (progress < -1.6 || progress > 1.6) {
                continue;
            }

            item.element.style.transform = `translate3d(${(progress * item.x).toFixed(1)}px, ${(progress * item.y).toFixed(1)}px, 0) rotate(${(progress * item.rotate).toFixed(2)}deg)`;

            if (item.fade) {
                item.element.style.opacity = clamp(1 - Math.max(0, progress) * item.fade).toFixed(3);
            }
        }
    });
}

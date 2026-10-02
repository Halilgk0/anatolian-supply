import { clamp, onFrame, onMeasure, reducedMotion } from './motion';

/**
 * Fills the ordering route line with scroll and marks each step once the line reaches it.
 */
export function initRoute() {
    const section = document.querySelector('[data-route]');

    if (!section) {
        return;
    }

    const line = section.querySelector('[data-route-line]');
    const steps = [...section.querySelectorAll('[data-step]')];

    if (reducedMotion) {
        section.style.setProperty('--route', '1');
        steps.forEach((step) => step.setAttribute('data-reached', ''));

        return;
    }

    let lineTop = 0;
    let lineLength = 1;
    let horizontal = false;
    let thresholds = [];

    onMeasure(() => {
        const rect = line.getBoundingClientRect();

        horizontal = rect.width > rect.height;
        lineTop = rect.top + window.scrollY;
        lineLength = Math.max(1, horizontal ? rect.width : rect.height);
        thresholds = steps.map((step) => {
            const marker = step.querySelector('[data-step-marker]').getBoundingClientRect();

            return horizontal
                ? (marker.left + marker.width / 2 - rect.left) / rect.width
                : (marker.top + marker.height / 2 - rect.top) / rect.height;
        });
    });

    onFrame(({ y, height }) => {
        const progress = horizontal
            ? clamp((y + height * 0.8 - lineTop) / (height * 0.4))
            : clamp((y + height * 0.62 - lineTop) / lineLength);

        section.style.setProperty('--route', progress.toFixed(4));
        steps.forEach((step, index) => {
            step.toggleAttribute('data-reached', progress > 0 && progress >= thresholds[index] - 0.01);
        });
    });
}

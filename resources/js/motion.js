/**
 * Shared scroll loop. Modules register a measure step (layout reads, run on load and resize)
 * and a frame step (style writes, run once per animation frame while the page scrolls).
 */
export const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

export const viewport = {
    y: window.scrollY,
    width: window.innerWidth,
    height: window.innerHeight,
    velocity: 0,
};

const measureSteps = [];
const frameSteps = [];
let lastY = window.scrollY;
let frameQueued = false;

export const clamp = (value, min = 0, max = 1) => Math.min(max, Math.max(min, value));

export const pageTop = (element) => element.getBoundingClientRect().top + window.scrollY;

export function onMeasure(step) {
    measureSteps.push(step);
}

export function onFrame(step) {
    frameSteps.push(step);
}

function runFrame() {
    frameQueued = false;
    const y = window.scrollY;
    viewport.velocity = y - lastY;
    viewport.y = y;
    lastY = y;
    frameSteps.forEach((step) => step(viewport));
}

export function requestFrame() {
    if (!frameQueued) {
        frameQueued = true;
        requestAnimationFrame(runFrame);
    }
}

export function measureAll() {
    viewport.width = window.innerWidth;
    viewport.height = window.innerHeight;
    measureSteps.forEach((step) => step(viewport));
    lastY = window.scrollY;
    requestFrame();
}

export function startMotion() {
    let lastWidth = window.innerWidth;
    let lastHeight = window.innerHeight;
    let resizeTimer;

    window.addEventListener('scroll', requestFrame, { passive: true });

    window.addEventListener('resize', () => {
        // Mobile toolbars change the height while scrolling; re-measuring then makes pinned sections jump.
        const widthChanged = window.innerWidth !== lastWidth;
        const heightJumped = Math.abs(window.innerHeight - lastHeight) > 160;

        if (!widthChanged && !heightJumped) {
            requestFrame();

            return;
        }

        lastWidth = window.innerWidth;
        lastHeight = window.innerHeight;
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(measureAll, 120);
    });

    window.addEventListener('load', measureAll);
    document.fonts?.ready.then(measureAll);

    measureAll();
}

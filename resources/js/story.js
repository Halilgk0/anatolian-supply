import { clamp, onFrame, onMeasure, pageTop, reducedMotion } from './motion';

const SVG_NS = 'http://www.w3.org/2000/svg';

/**
 * Draws nested, wobbly contour rings around a peak, like a topographic map sheet.
 */
export function initContours() {
    document.querySelectorAll('[data-contours]').forEach((svg) => {
        const [centerX, centerY] = svg.dataset.contours.split(',').map(Number);
        const group = document.createElementNS(SVG_NS, 'g');

        group.setAttribute('fill', 'none');
        group.setAttribute('stroke', 'currentColor');

        for (let ring = 0; ring < 16; ring++) {
            const base = 26 + ring * 34;
            let d = '';

            for (let step = 0; step <= 120; step++) {
                const t = (step / 120) * Math.PI * 2;
                const wobble = 1 + 0.16 * Math.sin(3 * t + 0.6 + ring * 0.12) + 0.09 * Math.sin(5 * t + 2.1 - ring * 0.1) + 0.05 * Math.sin(9 * t + ring * 0.2);
                const x = centerX + Math.cos(t) * base * wobble * 1.25;
                const y = centerY + Math.sin(t) * base * wobble;

                d += `${step ? 'L' : 'M'}${x.toFixed(1)},${y.toFixed(1)}`;
            }

            const path = document.createElementNS(SVG_NS, 'path');

            path.setAttribute('d', `${d}Z`);
            // Every fourth line is an index contour, drawn heavier as on survey maps.
            path.setAttribute('stroke-width', ring % 4 === 3 ? '2' : '1');
            group.append(path);
        }

        svg.prepend(group);
    });
}

/**
 * Lights up a paragraph word by word as it scrolls through the viewport, and dims it again on the way back.
 */
export function initWordReveal() {
    if (reducedMotion) {
        return;
    }

    document.querySelectorAll('[data-reveal-words]').forEach((paragraph) => {
        const words = paragraph.textContent.trim().split(/\s+/);

        paragraph.textContent = '';
        words.forEach((word, index) => {
            const span = document.createElement('span');

            span.className = 'word';
            span.textContent = word;
            paragraph.append(span, index < words.length - 1 ? ' ' : '');
        });

        const spans = [...paragraph.querySelectorAll('.word')];
        let start = 0;
        let end = 1;
        let litCount = -1;

        onMeasure(({ height }) => {
            const top = pageTop(paragraph);

            start = top - height * 0.85;
            end = top + paragraph.offsetHeight - height * 0.45;
        });

        onFrame(({ y }) => {
            const count = Math.round(clamp((y - start) / (end - start)) * spans.length);

            if (count === litCount) {
                return;
            }

            litCount = count;
            spans.forEach((span, index) => {
                span.style.opacity = index < count ? '1' : '0.16';
            });
        });
    });
}

import { onFrame, requestFrame } from './motion';

/**
 * Hides the header while scrolling down, brings it back on the way up,
 * and handles the full-screen mobile menu.
 */
export function initHeader() {
    const header = document.querySelector('[data-site-header]');

    if (!header) {
        return;
    }

    const overHero = header.hasAttribute('data-over-hero');
    const hero = document.querySelector('[data-hero]');
    const toggle = header.querySelector('[data-menu-toggle]');
    const toggleLabel = toggle.querySelector('.sr-only');
    const menu = header.querySelector('[data-menu]');
    let menuOpen = false;

    onFrame(({ y, velocity }) => {
        if (menuOpen) {
            return;
        }

        if (overHero) {
            header.toggleAttribute('data-solid', y > (hero?.offsetHeight ?? 0) - 96);
        }

        if (y < 96 || velocity < -3) {
            header.removeAttribute('data-hidden');
        } else if (velocity > 3) {
            header.setAttribute('data-hidden', '');
        }
    });

    header.addEventListener('focusin', () => header.removeAttribute('data-hidden'));

    const setMenu = (open) => {
        menuOpen = open;
        menu.hidden = !open;
        toggle.setAttribute('aria-expanded', String(open));
        toggleLabel.textContent = open ? 'Menüyü kapat' : 'Menüyü aç';
        header.toggleAttribute('data-menu-open', open);
        document.documentElement.classList.toggle('overflow-hidden', open);

        if (open) {
            header.setAttribute('data-solid', '');
            header.removeAttribute('data-hidden');
        } else {
            requestFrame();
        }
    };

    toggle.addEventListener('click', () => setMenu(!menuOpen));
    menu.querySelectorAll('[data-menu-link]').forEach((link) => link.addEventListener('click', () => setMenu(false)));

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && menuOpen) {
            setMenu(false);
            toggle.focus();
        }
    });

    window.matchMedia('(width >= 48rem)').addEventListener('change', (event) => {
        if (event.matches && menuOpen) {
            setMenu(false);
        }
    });
}

import { reducedMotion } from './motion';

const FAILURE_MESSAGE = 'Talep gönderilemedi. Hazır mesajı e-posta uygulamanda açmayı ya da Instagram’dan yazmayı dene.';

/**
 * Colour and size pickers, the inquiry dialog and its form submission.
 */
export function initProductPage() {
    const page = document.querySelector('[data-product-page]');

    if (!page) {
        return;
    }

    const art = page.querySelector('[data-art]');
    const photo = page.querySelector('[data-photo]');
    const thumbs = [...page.querySelectorAll('[data-gallery-thumb]')];
    const colorLabel = page.querySelector('[data-color-label]');
    const dialog = page.querySelector('[data-inquiry-dialog]');
    const form = dialog.querySelector('[data-inquiry-form]');
    const colorInput = form.querySelector('[data-inquiry-color]');
    const sizeInput = form.querySelector('[data-inquiry-size]');
    const summaryColor = dialog.querySelector('[data-summary-color]');
    const summarySize = dialog.querySelector('[data-summary-size]');
    const mailtoLinks = dialog.querySelectorAll('[data-mailto]');
    const { mailTo, mailSubject, mailBody } = page.dataset;

    const showPhoto = (src, cutout) => {
        const image = photo?.querySelector('img');

        if (!image || image.getAttribute('src') === src) {
            return;
        }

        thumbs.forEach((thumb) => thumb.setAttribute('aria-pressed', String(thumb.dataset.src === src)));
        photo.classList.add('is-swapping');

        const next = new Image();
        next.src = src;
        next.decode()
            .catch(() => {})
            .finally(() => {
                image.src = src;
                photo.toggleAttribute('data-cutout', cutout);
                photo.classList.remove('is-swapping');
            });
    };

    const syncSelection = (changed) => {
        const color = page.querySelector('input[name="color-choice"]:checked');
        const size = page.querySelector('input[name="size-choice"]:checked');

        if (color) {
            if (art) {
                art.style.setProperty('--art-main', color.dataset.main);
                art.style.setProperty('--art-shade', color.dataset.shade);
                art.style.setProperty('--art-detail', color.dataset.detail);
                art.setAttribute('aria-label', art.getAttribute('aria-label').replace(/, [^,]+ renk$/, `, ${color.value} renk`));
            }

            // A colour with its own photo swaps the main photo, but only when the visitor picks it.
            if (changed === color && color.dataset.image) {
                showPhoto(color.dataset.image, color.hasAttribute('data-cutout'));
            }

            colorLabel.textContent = color.value;
            colorInput.value = color.value;
        }

        if (size) {
            sizeInput.value = size.value;
        }

        if (summaryColor) {
            summaryColor.textContent = colorInput.value;
        }

        if (summarySize) {
            summarySize.textContent = sizeInput.value || 'seçilmedi';
        }

        const extras = [colorInput.value && `Seçilen renk: ${colorInput.value}`, sizeInput.value && `Seçilen beden: ${sizeInput.value}`].filter(Boolean);
        const href = `mailto:${mailTo}?subject=${encodeURIComponent(mailSubject)}&body=${encodeURIComponent([mailBody, ...extras].join('\n'))}`;

        mailtoLinks.forEach((link) => link.setAttribute('href', href));
    };

    page.addEventListener('change', (event) => {
        if (event.target.matches('input[name="color-choice"], input[name="size-choice"]')) {
            syncSelection(event.target);
        }
    });

    thumbs.forEach((thumb) => thumb.addEventListener('click', () => showPhoto(thumb.dataset.src, thumb.hasAttribute('data-cutout'))));

    syncSelection(null);
    initDialog(page, dialog);
    initInquiryForm(dialog, form);
    initMobileBar(page);
    initTilt(page);
}

function initDialog(page, dialog) {
    const body = dialog.querySelector('[data-inquiry-body]');
    const success = dialog.querySelector('[data-inquiry-success]');

    const open = () => {
        if (!success.hidden) {
            success.hidden = true;
            body.hidden = false;
        }

        dialog.showModal();
        dialog.querySelector('input:not([type="hidden"]):not([tabindex="-1"])')?.focus();
    };

    page.querySelectorAll('[data-open-inquiry]').forEach((button) => button.addEventListener('click', open));
    dialog.querySelectorAll('[data-close-inquiry]').forEach((button) => button.addEventListener('click', () => dialog.close()));

    // A click that lands on the dialog element itself is a click on the backdrop.
    dialog.addEventListener('click', (event) => {
        if (event.target === dialog) {
            dialog.close();
        }
    });

    dialog.addEventListener('close', () => document.documentElement.classList.remove('overflow-hidden'));
    dialog.addEventListener('toggle', () => document.documentElement.classList.toggle('overflow-hidden', dialog.open));

    // Server-side validation errors from a non-JS submission: reopen as a proper modal.
    if (dialog.hasAttribute('data-open-on-load')) {
        dialog.close();
        dialog.showModal();
    }
}

function initInquiryForm(dialog, form) {
    const status = form.querySelector('[data-inquiry-status]');
    const submit = form.querySelector('[data-inquiry-submit]');
    const submitLabel = submit.querySelector('[data-submit-label]');
    const body = dialog.querySelector('[data-inquiry-body]');
    const success = dialog.querySelector('[data-inquiry-success]');

    const clearErrors = () => {
        status.textContent = '';
        form.querySelectorAll('[data-error-for]').forEach((error) => {
            error.textContent = '';
        });
        form.querySelectorAll('[aria-invalid]').forEach((field) => field.removeAttribute('aria-invalid'));
    };

    const showErrors = (errors = {}) => {
        let firstField = null;

        Object.entries(errors).forEach(([name, messages]) => {
            const error = form.querySelector(`[data-error-for="${name}"]`);
            const field = form.querySelector(`[name="${name}"]`);

            if (error && field) {
                error.textContent = messages[0];
                field.setAttribute('aria-invalid', 'true');
                firstField ??= field;
            } else {
                status.textContent = messages[0];
            }
        });

        firstField?.focus();
    };

    const setBusy = (busy) => {
        submit.disabled = busy;
        submitLabel.textContent = busy ? 'Gönderiliyor…' : 'Talebi gönder';
    };

    form.addEventListener('submit', async (event) => {
        event.preventDefault();
        clearErrors();
        setBusy(true);

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                headers: { Accept: 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: new FormData(form),
            });
            const data = await response.json().catch(() => ({}));

            if (response.ok) {
                dialog.querySelector('[data-success-message]').textContent = data.message;
                body.hidden = true;
                success.hidden = false;
                success.querySelector('button').focus();
                form.reset();

                return;
            }

            if (response.status === 422) {
                showErrors(data.errors);
            } else if (response.status === 429) {
                status.textContent = 'Kısa sürede çok fazla talep gönderildi. Bir dakika bekleyip tekrar dene.';
            } else if (response.status === 419) {
                status.textContent = 'Sayfanın oturum süresi doldu. Sayfayı yenileyip tekrar dene.';
            } else {
                status.textContent = data.message || FAILURE_MESSAGE;
            }
        } catch {
            status.textContent = 'Sunucuya ulaşılamadı. İnternet bağlantını kontrol edip tekrar dene.';
        } finally {
            setBusy(false);
        }
    });
}

function initMobileBar(page) {
    const bar = page.querySelector('[data-mobile-cta]');
    const primary = page.querySelector('[data-primary-cta]');

    if (!bar || !primary) {
        return;
    }

    new IntersectionObserver(([entry]) => {
        const show = !entry.isIntersecting && entry.boundingClientRect.top < 0;

        bar.classList.toggle('translate-y-full', !show);
        bar.inert = !show;
    }).observe(primary);
}

function initTilt(page) {
    if (reducedMotion || !window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
        return;
    }

    page.querySelectorAll('[data-tilt]').forEach((stage) => {
        const target = stage.querySelector('[data-tilt-target]');

        stage.addEventListener('pointermove', (event) => {
            const rect = stage.getBoundingClientRect();
            const x = (event.clientX - rect.left) / rect.width - 0.5;
            const y = (event.clientY - rect.top) / rect.height - 0.5;

            target.style.transform = `rotateY(${(x * 16).toFixed(2)}deg) rotateX(${(-y * 12).toFixed(2)}deg)`;
        });

        stage.addEventListener('pointerleave', () => {
            target.style.transform = '';
        });
    });
}

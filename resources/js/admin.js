/**
 * Product admin: photo uploads (shrunk in the browser first), repeatable colour and detail rows,
 * custom sizes, a live preview of the product card, and unsaved-change protection.
 */

const MAX_SIDE = 1600;
const MAX_PHOTOS = 8;
// PHP accepts 2 MB per file by default, so every photo is shrunk below this.
const TARGET_BYTES = 0.9 * 1024 * 1024;
// Vercel rejects requests above 4.5 MB, so one save may carry at most this much in new photos.
const MAX_REQUEST_BYTES = 4 * 1024 * 1024;

document.querySelectorAll('form[data-confirm]').forEach((form) => {
    form.addEventListener('submit', (event) => {
        if (!window.confirm(form.dataset.confirm)) {
            event.preventDefault();
        }
    });
});

const form = document.querySelector('[data-product-form]');

if (form) {
    const busy = createBusyTracker(form);

    initImages(form, busy);
    initColors(form, busy);
    initSpecs(form);
    initSizes(form);
    initPreviewText(form);
    initCounters(form);
    initUnsavedWarning(form, busy);
}

function createBusyTracker(productForm) {
    const save = productForm.querySelector('[data-save]');
    let pending = 0;

    return {
        get pending() {
            return pending;
        },
        start() {
            pending++;
            save.disabled = true;
        },
        finish() {
            pending = Math.max(0, pending - 1);
            save.disabled = pending > 0;
        },
    };
}

/**
 * Decode, rotate and shrink a photo so it uploads quickly. Transparent PNGs keep their transparency.
 */
async function prepareImage(file) {
    let bitmap;

    try {
        bitmap = await createImageBitmap(file, { imageOrientation: 'from-image' });
    } catch {
        return { file, cutout: false, url: URL.createObjectURL(file) };
    }

    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d', { willReadFrequently: true });
    let scale = Math.min(1, MAX_SIDE / Math.max(bitmap.width, bitmap.height));

    const draw = () => {
        canvas.width = Math.round(bitmap.width * scale);
        canvas.height = Math.round(bitmap.height * scale);
        context.clearRect(0, 0, canvas.width, canvas.height);
        context.drawImage(bitmap, 0, 0, canvas.width, canvas.height);
    };

    draw();

    const cutout = file.type !== 'image/jpeg' && hasTransparentEdges(context, canvas.width, canvas.height);

    if (scale === 1 && file.size <= TARGET_BYTES) {
        return { file, cutout, url: URL.createObjectURL(file) };
    }

    let blob = null;

    for (const quality of [0.86, 0.78, 0.7, 0.6]) {
        blob = await encode(canvas, cutout, quality);

        if (blob && blob.size <= TARGET_BYTES) {
            break;
        }

        scale *= 0.85;
        draw();
    }

    if (!blob) {
        return { file, cutout, url: URL.createObjectURL(file) };
    }

    const extension = blob.type.split('/')[1].replace('jpeg', 'jpg');
    const name = `${file.name.replace(/\.[^.]+$/, '') || 'foto'}.${extension}`;
    const prepared = new File([blob], name, { type: blob.type, lastModified: Date.now() });

    return { file: prepared, cutout, url: URL.createObjectURL(prepared) };
}

async function encode(canvas, keepTransparency, quality) {
    const toBlob = (source, type) => new Promise((resolve) => source.toBlob(resolve, type, quality));

    if (keepTransparency) {
        const webp = await toBlob(canvas, 'image/webp');

        // Browsers without WebP encoding fall back to PNG.
        return webp?.type === 'image/webp' ? webp : toBlob(canvas, 'image/png');
    }

    // JPEG has no transparency: put the photo on white first so see-through areas do not turn black.
    const flat = document.createElement('canvas');
    flat.width = canvas.width;
    flat.height = canvas.height;
    const context = flat.getContext('2d');
    context.fillStyle = '#ffffff';
    context.fillRect(0, 0, flat.width, flat.height);
    context.drawImage(canvas, 0, 0);

    return toBlob(flat, 'image/jpeg');
}

function hasTransparentEdges(context, width, height) {
    const right = width - 2;
    const bottom = height - 2;
    const points = [[1, 1], [right, 1], [1, bottom], [right, bottom], [width >> 1, 1], [width >> 1, bottom], [1, height >> 1], [right, height >> 1]];

    return points.filter(([x, y]) => context.getImageData(Math.max(0, x), Math.max(0, y), 1, 1).data[3] < 128).length >= 6;
}

function initImages(productForm, busy) {
    const manager = productForm.querySelector('[data-image-manager]');
    const input = manager.querySelector('[data-image-input]');
    const dropzone = manager.querySelector('[data-dropzone]');
    const list = manager.querySelector('[data-image-list]');
    const template = manager.querySelector('[data-new-image-template]');
    const status = manager.querySelector('[data-image-status]');
    const preview = productForm.querySelector('[data-preview-visual]');
    const initialPreview = preview.innerHTML;
    const added = [];

    const keptCards = () => [...list.querySelectorAll('[data-image-card]')].filter((card) => !card.querySelector('[data-remove-image]:checked'));

    const updatePreview = () => {
        const checked = list.querySelector('input[name="main_image"]:checked');
        const card = checked?.closest('[data-image-card]');

        if (!card || card.querySelector('[data-remove-image]:checked')) {
            preview.innerHTML = keptCards().length ? preview.innerHTML : initialPreview;

            return;
        }

        const photo = document.createElement('div');
        photo.className = 'product-photo h-44 w-full';
        photo.toggleAttribute('data-cutout', card.hasAttribute('data-cutout'));
        photo.innerHTML = '<span class="print"><img alt=""><span class="tape"></span></span>';
        photo.querySelector('img').src = card.dataset.src;
        preview.replaceChildren(photo);
    };

    const ensureMain = () => {
        const radios = keptCards().map((card) => card.querySelector('input[name="main_image"]'));

        if (!radios.some((radio) => radio.checked) && radios[0]) {
            radios[0].checked = true;
        }

        updatePreview();
    };

    const cutouts = manager.querySelector('[data-image-cutouts]');

    const syncInput = () => {
        const transfer = new DataTransfer();

        cutouts.replaceChildren();

        added.forEach((item, index) => {
            transfer.items.add(item.file);
            item.card.querySelector('input[name="main_image"]').value = `new:${index}`;

            const flag = document.createElement('input');

            flag.type = 'hidden';
            flag.name = 'image_cutouts[]';
            flag.value = item.cutout ? '1' : '0';
            cutouts.append(flag);
        });

        input.files = transfer.files;
        ensureMain();
    };

    const addFiles = async (files) => {
        const all = [...files];
        const photos = all.filter((file) => /^image\/(jpeg|png|webp)$/.test(file.type));
        const room = MAX_PHOTOS - keptCards().length;
        const accepted = photos.slice(0, Math.max(0, room));
        const notes = [];

        if (photos.length < all.length) {
            notes.push(`${all.length - photos.length} dosya JPEG, PNG ya da WEBP olmadığı için eklenmedi.`);
        }

        if (accepted.length < photos.length) {
            notes.push(`En fazla ${MAX_PHOTOS} fotoğraf eklenebilir; ${photos.length - accepted.length} tanesi eklenmedi.`);
        }

        if (accepted.length === 0) {
            syncInput();
            status.textContent = notes.join(' ');

            return;
        }

        busy.start();
        status.textContent = `${accepted.length} fotoğraf hazırlanıyor…`;

        for (const file of accepted) {
            const prepared = await prepareImage(file);
            const card = template.content.firstElementChild.cloneNode(true);

            card.dataset.src = prepared.url;
            card.toggleAttribute('data-cutout', prepared.cutout);
            card.querySelector('img').src = prepared.url;
            card.querySelector('[data-kind]').textContent = prepared.cutout ? 'Yeni, dekupe PNG' : 'Yeni fotoğraf';
            card.querySelector('[data-remove-new]').addEventListener('click', () => {
                added.splice(added.indexOf(item), 1);
                URL.revokeObjectURL(prepared.url);
                card.remove();
                syncInput();
            });

            const item = { ...prepared, card };

            added.push(item);
            list.append(card);
        }

        syncInput();
        status.textContent = notes.join(' ');
        busy.finish();
    };

    input.addEventListener('change', () => addFiles(input.files));

    dropzone.addEventListener('dragover', (event) => {
        event.preventDefault();
        dropzone.setAttribute('data-dragging', '');
    });

    dropzone.addEventListener('dragleave', () => dropzone.removeAttribute('data-dragging'));

    dropzone.addEventListener('drop', (event) => {
        event.preventDefault();
        dropzone.removeAttribute('data-dragging');
        addFiles(event.dataTransfer.files);
    });

    list.addEventListener('change', (event) => {
        if (event.target.matches('[data-remove-image]')) {
            ensureMain();
        } else if (event.target.matches('input[name="main_image"]')) {
            updatePreview();
        }
    });

    ensureMain();
}

function initColors(productForm, busy) {
    const container = productForm.querySelector('[data-colors]');
    const list = container.querySelector('[data-color-list]');
    const template = container.querySelector('[data-color-template]');
    let nextIndex = Number(container.dataset.nextIndex) || 0;

    const addRow = (name = '', hex = '#5f6744') => {
        const html = template.innerHTML.replaceAll('__INDEX__', String(nextIndex++));
        const holder = document.createElement('ul');

        holder.innerHTML = html.trim();

        const row = holder.firstElementChild;

        row.querySelector('input[type="color"]').value = hex;
        row.querySelector('input[type="text"]').value = name;
        list.append(row);
        productForm.dispatchEvent(new Event('input', { bubbles: true }));

        return row;
    };

    container.addEventListener('click', (event) => {
        const preset = event.target.closest('[data-color-preset]');

        if (preset) {
            const existing = [...list.querySelectorAll('input[type="text"]')].find((field) => field.value.trim().toLocaleLowerCase('tr') === preset.dataset.name.toLocaleLowerCase('tr'));

            if (existing) {
                existing.focus();

                return;
            }

            addRow(preset.dataset.name, preset.dataset.hex);
        }

        if (event.target.closest('[data-add-color]')) {
            addRow().querySelector('input[type="text"]').focus();
        }

        const remove = event.target.closest('[data-remove-row]');

        if (remove) {
            remove.closest('[data-color-row]').remove();
            productForm.dispatchEvent(new Event('input', { bubbles: true }));
        }
    });

    container.addEventListener('change', async (event) => {
        if (!event.target.matches('[data-color-image]') || !event.target.files[0]) {
            return;
        }

        const field = event.target;
        const row = field.closest('[data-color-row]');

        busy.start();

        const prepared = await prepareImage(field.files[0]);
        const transfer = new DataTransfer();

        transfer.items.add(prepared.file);
        field.files = transfer.files;

        const flagName = field.name.replace('[image]', '[image_cutout]');
        let flag = row.querySelector(`input[name="${CSS.escape(flagName)}"]`);

        if (!flag) {
            flag = document.createElement('input');
            flag.type = 'hidden';
            flag.name = flagName;
            row.append(flag);
        }

        flag.value = prepared.cutout ? '1' : '0';

        const preview = row.querySelector('[data-color-preview]');

        preview.src = prepared.url;
        preview.hidden = false;
        row.querySelector('[data-color-photo-label]').textContent = 'Fotoğrafı değiştir';

        const removeExisting = row.querySelector('[data-color-remove-image]');

        if (removeExisting) {
            removeExisting.checked = false;
        }

        busy.finish();
    });
}

function initSpecs(productForm) {
    const container = productForm.querySelector('[data-specs]');
    const list = container.querySelector('[data-spec-list]');
    const template = container.querySelector('[data-spec-template]');
    let nextIndex = Number(container.dataset.nextIndex) || 0;

    container.addEventListener('click', (event) => {
        if (event.target.closest('[data-add-spec]')) {
            const holder = document.createElement('ul');

            holder.innerHTML = template.innerHTML.replaceAll('__INDEX__', String(nextIndex++)).trim();
            list.append(holder.firstElementChild);
            list.lastElementChild.querySelector('input').focus();
        }

        const remove = event.target.closest('[data-remove-row]');

        if (remove) {
            remove.closest('[data-spec-row]').remove();
            productForm.dispatchEvent(new Event('input', { bubbles: true }));
        }
    });
}

function initSizes(productForm) {
    const container = productForm.querySelector('[data-sizes]');
    const field = container.querySelector('[data-size-input]');
    const custom = container.querySelector('[data-custom-sizes]');

    const addSize = () => {
        const value = field.value.trim();

        if (!value) {
            return;
        }

        const existing = [...container.querySelectorAll('input[name="sizes[]"]')].find((checkbox) => checkbox.value.toLocaleLowerCase('tr') === value.toLocaleLowerCase('tr'));

        if (existing) {
            existing.checked = true;
        } else {
            const label = document.createElement('label');

            label.className = 'cursor-pointer';
            label.innerHTML = '<input type="checkbox" name="sizes[]" class="sr-only" checked><span class="size-chip"></span>';
            label.querySelector('input').value = value;
            label.querySelector('.size-chip').textContent = value;
            custom.append(label);
        }

        field.value = '';
        field.focus();
        productForm.dispatchEvent(new Event('input', { bubbles: true }));
    };

    container.querySelector('[data-add-size]').addEventListener('click', addSize);

    field.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            addSize();
        }
    });
}

function initPreviewText(productForm) {
    productForm.querySelectorAll('[data-preview-source]').forEach((source) => {
        const target = productForm.querySelector(`[data-preview="${source.dataset.previewSource}"]`);

        source.addEventListener('input', () => {
            const value = source.value.trim();

            target.textContent = value || target.dataset.placeholder || '';
        });
    });
}

function initCounters(productForm) {
    productForm.querySelectorAll('[data-counter-for]').forEach((counter) => {
        const field = productForm.querySelector(`#${counter.dataset.counterFor}`);
        const update = () => {
            counter.textContent = `${field.value.length}/${field.maxLength}`;
        };

        field.addEventListener('input', update);
        update();
    });
}

function initUnsavedWarning(productForm, busy) {
    const hint = productForm.querySelector('[data-dirty-hint]');
    const save = productForm.querySelector('[data-save]');
    let dirty = false;
    let submitting = false;

    const markDirty = () => {
        dirty = true;
        hint.hidden = false;
    };

    productForm.addEventListener('input', markDirty);
    productForm.addEventListener('change', markDirty);

    productForm.addEventListener('submit', (event) => {
        if (busy.pending > 0) {
            event.preventDefault();

            return;
        }

        const uploadBytes = [...productForm.querySelectorAll('input[type="file"]')]
            .flatMap((input) => [...input.files])
            .reduce((total, file) => total + file.size, 0);

        if (uploadBytes > MAX_REQUEST_BYTES) {
            event.preventDefault();

            const status = productForm.querySelector('[data-image-status]');

            status.textContent = `Bu kayıtta ${(uploadBytes / 1024 / 1024).toFixed(1)} MB yeni fotoğraf var; bir kerede en fazla 4 MB yüklenebiliyor. Birkaç fotoğrafı çıkarıp kaydet, kalanları sonra “Düzenle” ile ekle.`;
            status.scrollIntoView({ behavior: 'smooth', block: 'center' });

            return;
        }

        submitting = true;
        save.disabled = true;
        save.textContent = 'Kaydediliyor…';
    });

    window.addEventListener('beforeunload', (event) => {
        if (dirty && !submitting) {
            event.preventDefault();
            event.returnValue = '';
        }
    });
}

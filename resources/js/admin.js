function initAdminImagePreviews() {
    document.querySelectorAll('[data-image-input]').forEach((input) => {
        input.addEventListener('change', () => {
            const file = input.files?.[0];
            const previewName = input.dataset.previewTarget;
            const preview = document.querySelector(`[data-preview-image="${previewName}"]`);
            const placeholder = document.querySelector(`[data-preview-placeholder="${previewName}"]`);
            const removeCheckbox = document.getElementById(input.dataset.removeTarget || '');

            if (!file || !preview) return;

            preview.src = URL.createObjectURL(file);
            preview.classList.remove('d-none');
            placeholder?.classList.add('d-none');
            if (removeCheckbox) removeCheckbox.checked = false;
        });
    });
}

function initAdminCharacterCounters() {
    document.querySelectorAll('[data-character-count]').forEach((counter) => {
        const field = document.getElementById(counter.dataset.characterCount);
        if (!field) return;

        const update = () => {
            const maximum = Number(field.getAttribute('maxlength')) || 0;
            counter.textContent = maximum ? `${field.value.length}/${maximum}` : `${field.value.length}`;
        };

        field.addEventListener('input', update);
        update();
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initAdminImagePreviews();
    initAdminCharacterCounters();
});

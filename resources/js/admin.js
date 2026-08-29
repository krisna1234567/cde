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

function initAdminDeleteConfirmations() {
    document.querySelectorAll('form[data-confirm-delete]').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const subject = form.dataset.confirmDelete || 'data ini';
            if (!window.confirm(`Hapus ${subject}? Tindakan ini tidak dapat dibatalkan.`)) event.preventDefault();
        });
    });
}

function sanitizeEditorHtml(html) {
    const template = document.createElement('template');
    template.innerHTML = html;
    const allowedTags = new Set(['P', 'BR', 'STRONG', 'B', 'EM', 'I', 'U', 'A']);

    const cleanNode = (node) => {
        [...node.childNodes].forEach((child) => {
            if (child.nodeType === Node.ELEMENT_NODE) {
                cleanNode(child);

                if (!allowedTags.has(child.tagName)) {
                    child.replaceWith(...child.childNodes);
                    return;
                }

                [...child.attributes].forEach((attribute) => {
                    if (child.tagName !== 'A' || attribute.name !== 'href') {
                        child.removeAttribute(attribute.name);
                    }
                });

                if (child.tagName === 'A') {
                    const href = child.getAttribute('href') || '';
                    const safeHref = /^(https?:\/\/|mailto:|tel:|\/|#)/i.test(href);
                    if (!safeHref) {
                        child.replaceWith(...child.childNodes);
                        return;
                    }
                    child.setAttribute('target', '_blank');
                    child.setAttribute('rel', 'noopener');
                }
            } else if (child.nodeType !== Node.TEXT_NODE) {
                child.remove();
            }
        });
    };

    cleanNode(template.content);

    return template.innerHTML
        .replace(/<div><br><\/div>/gi, '')
        .replace(/<div>/gi, '<p>')
        .replace(/<\/div>/gi, '</p>')
        .trim();
}

function normalizeEditorHtml(value) {
    const html = value.trim();
    if (html === '') return '<p><br></p>';
    if (/<[a-z][\s\S]*>/i.test(html)) return sanitizeEditorHtml(html);

    return html
        .split(/\n{2,}/)
        .map((paragraph) => paragraph.trim())
        .filter(Boolean)
        .map((paragraph) => `<p>${paragraph.replace(/\n/g, '<br>')}</p>`)
        .join('');
}

function initAdminWysiwygEditors() {
    document.querySelectorAll('textarea[data-wysiwyg]').forEach((textarea) => {
        const wrapper = document.createElement('div');
        wrapper.className = 'admin-wysiwyg';

        const toolbar = document.createElement('div');
        toolbar.className = 'admin-wysiwyg-toolbar';
        toolbar.innerHTML = `
            <button type="button" data-command="bold" title="Bold"><i class="bi bi-type-bold"></i></button>
            <button type="button" data-command="italic" title="Italic"><i class="bi bi-type-italic"></i></button>
            <button type="button" data-command="underline" title="Underline"><i class="bi bi-type-underline"></i></button>
            <button type="button" data-command="createLink" title="Link"><i class="bi bi-link-45deg"></i></button>
            <button type="button" data-command="removeFormat" title="Clear formatting"><i class="bi bi-eraser"></i></button>
        `;

        const editor = document.createElement('div');
        editor.className = 'admin-wysiwyg-editor';
        editor.contentEditable = 'true';
        editor.innerHTML = normalizeEditorHtml(textarea.value);

        textarea.classList.add('admin-wysiwyg-source');
        textarea.parentNode.insertBefore(wrapper, textarea);
        wrapper.appendChild(toolbar);
        wrapper.appendChild(editor);
        wrapper.appendChild(textarea);

        const syncTextarea = () => {
            const sanitized = sanitizeEditorHtml(editor.innerHTML);
            textarea.value = editor.textContent.trim() === '' ? '' : sanitized;
        };

        toolbar.querySelectorAll('button[data-command]').forEach((button) => {
            button.addEventListener('click', () => {
                editor.focus();
                const command = button.dataset.command;

                if (command === 'createLink') {
                    const url = window.prompt('URL link');
                    if (!url) return;
                    document.execCommand(command, false, url);
                } else {
                    document.execCommand(command, false, null);
                }

                syncTextarea();
            });
        });

        editor.addEventListener('input', syncTextarea);
        editor.addEventListener('blur', syncTextarea);
        editor.addEventListener('paste', (event) => {
            event.preventDefault();
            const text = event.clipboardData?.getData('text/plain') || '';
            document.execCommand('insertText', false, text);
            syncTextarea();
        });
        textarea.form?.addEventListener('submit', syncTextarea);
        syncTextarea();
    });
}

document.addEventListener('DOMContentLoaded', () => {
    initAdminImagePreviews();
    initAdminCharacterCounters();
    initAdminDeleteConfirmations();
    initAdminWysiwygEditors();
});

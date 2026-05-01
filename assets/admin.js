class UniqueHeaders {
    constructor() {
        this.name = String(custom_meta_image_name);
        this.fileFrame = null;

        this.renderFeaturedImage();
        this.bindEvents();
    }

    bindEvents() {
        const setBtn = document.getElementById('set-' + this.name + '-thumbnail');
        if (setBtn) {
            setBtn.addEventListener('click', (evt) => {
                evt.preventDefault();
                this.renderMediaUploader();
            });
        }

        const removeBtn = document.getElementById('remove-' + this.name + '-thumbnail');
        if (removeBtn) {
            removeBtn.addEventListener('click', (evt) => {
                evt.preventDefault();
                this.resetUploadForm();
            });
        }
    }

    renderMediaUploader() {
        if (this.fileFrame) {
            this.fileFrame.open();
            return;
        }

        this.fileFrame = wp.media.frames.fileFrame = wp.media({
            frame: 'select',
            multiple: false,
        });

        this.fileFrame.on('select', () => {
            const json = this.fileFrame.state().get('selection').first().toJSON();

            if (!json.url) {
                return;
            }

            const container = document.getElementById(this.name + '-container');
            const img = container.querySelector('img');

            img.setAttribute('src', json.url);
            img.setAttribute('alt', json.caption);
            img.setAttribute('title', json.title);
            img.style.display = 'block';
            container.classList.remove('hidden');

            container.previousElementSibling.style.display = 'none';
            container.nextElementSibling.style.display = 'block';

            document.getElementById(this.name + '-id').value = json.id;
        });

        this.fileFrame.open();
    }

    resetUploadForm() {
        const container = document.getElementById(this.name + '-container');
        const img = container.querySelector('img');

        img.style.display = 'none';
        container.previousElementSibling.style.display = 'block';

        const next = container.nextElementSibling;
        next.style.display = 'none';
        next.classList.add('hidden');

        document.getElementById(this.name + '-info').querySelector('input').value = '';
    }

    renderFeaturedImage() {
        const input = document.getElementById(this.name + '-id');
        if (input.value.trim() === '') {
            return;
        }

        document.getElementById(this.name + '-container').classList.remove('hidden');

        const setLink = document.getElementById('set-' + this.name + '-thumbnail');
        if (setLink) {
            setLink.parentElement.style.display = 'none';
        }

        const removeLink = document.getElementById('remove-' + this.name + '-thumbnail');
        if (removeLink) {
            removeLink.parentElement.classList.remove('hidden');
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new UniqueHeaders();
});

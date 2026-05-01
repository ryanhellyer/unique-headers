declare const custom_meta_image_name: string[];
declare const wp: any;

class UniqueHeaders {
    private name: string;
    private fileFrame: any = null;

    constructor() {
        this.name = String(custom_meta_image_name);
        this.renderFeaturedImage();
        this.bindEvents();
    }

    private bindEvents(): void {
        const setBtn = document.getElementById('set-' + this.name + '-thumbnail');
        if (setBtn) {
            setBtn.addEventListener('click', (evt: Event) => {
                evt.preventDefault();
                this.renderMediaUploader();
            });
        }

        const removeBtn = document.getElementById('remove-' + this.name + '-thumbnail');
        if (removeBtn) {
            removeBtn.addEventListener('click', (evt: Event) => {
                evt.preventDefault();
                this.resetUploadForm();
            });
        }
    }

    private renderMediaUploader(): void {
        if (this.fileFrame) {
            this.fileFrame.open();
            return;
        }

        // Shortcode UI plugin hooks into all wp.media frames and crashes
        // when wpActiveEditor is undefined. Set a dummy before creating the
        // frame, then restore the original state immediately after.
        const activeEditor = (window as any).wpActiveEditor;
        if (activeEditor === undefined) {
            (window as any).wpActiveEditor = 'unique-headers-media';
        }

        this.fileFrame = wp.media.frames.fileFrame = wp.media({
            frame: 'select',
            multiple: false,
        });

        if (activeEditor === undefined) {
            delete (window as any).wpActiveEditor;
        }

        this.fileFrame.on('select', () => {
            const json = this.fileFrame.state().get('selection').first().toJSON();

            if (!json.url) {
                return;
            }

            const container = document.getElementById(this.name + '-container')!;
            const img = container.querySelector('img') as HTMLImageElement;

            img.setAttribute('src', json.url);
            img.setAttribute('alt', json.caption);
            img.setAttribute('title', json.title);
            img.style.display = 'block';
            container.classList.remove('hidden');

            (container.previousElementSibling as HTMLElement).style.display = 'none';
            (container.nextElementSibling as HTMLElement).style.display = 'block';

            (document.getElementById(this.name + '-id') as HTMLInputElement).value = String(json.id);
        });

        this.fileFrame.open();
    }

    private resetUploadForm(): void {
        const container = document.getElementById(this.name + '-container')!;
        const img = container.querySelector('img') as HTMLImageElement;

        img.style.display = 'none';
        (container.previousElementSibling as HTMLElement).style.display = 'block';

        const next = container.nextElementSibling as HTMLElement;
        next.style.display = 'none';
        next.classList.add('hidden');

        (document.getElementById(this.name + '-info')!.querySelector('input') as HTMLInputElement).value = '';
    }

    private renderFeaturedImage(): void {
        const input = document.getElementById(this.name + '-id') as HTMLInputElement;
        if (input.value.trim() === '') {
            return;
        }

        document.getElementById(this.name + '-container')!.classList.remove('hidden');

        const setLink = document.getElementById('set-' + this.name + '-thumbnail');
        if (setLink) {
            (setLink.parentElement as HTMLElement).style.display = 'none';
        }

        const removeLink = document.getElementById('remove-' + this.name + '-thumbnail');
        if (removeLink) {
            (removeLink.parentElement as HTMLElement).classList.remove('hidden');
        }
    }
}

document.addEventListener('DOMContentLoaded', () => {
    new UniqueHeaders();
});

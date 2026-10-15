/**
 * Dynamic Content Builder for Berita
 * Supports Text, Image, Link, and Divider blocks
 */

class DynamicContentBuilder {
    constructor(containerId, options = {}) {
        this.container = document.getElementById(containerId);
        this.blocks = [];
        this.blockCounter = 0;
        this.initialized = false;
        this.options = {
            allowedBlocks: ['text', 'image', 'link', 'divider'],
            maxBlocks: 50,
            ...options
        };

        this.init();
    }

    init() {
        if (!this.container) {
            console.error('Dynamic Content Builder: Container not found');
            return;
        }

        this.createToolbar();
        this.createBlocksContainer();
        this.initSortable();
        this.bindEvents();
    }

    createToolbar() {
        const toolbar = document.createElement('div');
        toolbar.className = 'content-builder-toolbar mb-3';
        toolbar.innerHTML = `
            <div class="btn-group" role="group">
                <button type="button" class="btn btn-outline-primary btn-sm add-block" data-type="text">
                    <i class="fas fa-font"></i> Text
                </button>
                <button type="button" class="btn btn-outline-primary btn-sm add-block" data-type="image">
                    <i class="fas fa-image"></i> Image
                </button>
                <button type="button" class="btn btn-outline-primary btn-sm add-block" data-type="link">
                    <i class="fas fa-link"></i> Link
                </button>
                <button type="button" class="btn btn-outline-primary btn-sm add-block" data-type="divider">
                    <i class="fas fa-minus"></i> Divider
                </button>
            </div>
            <div class="ms-auto">
                <button type="button" class="btn btn-outline-secondary btn-sm toggle-preview">
                    <i class="fas fa-eye"></i> Preview
                </button>
            </div>
        `;
        this.container.appendChild(toolbar);
    }

    createBlocksContainer() {
        const blocksContainer = document.createElement('div');
        blocksContainer.className = 'content-blocks-container';
        blocksContainer.id = 'content-blocks-container';
        this.container.appendChild(blocksContainer);

        // Create preview container
        const previewContainer = document.createElement('div');
        previewContainer.className = 'content-preview-container d-none mt-3';
        previewContainer.id = 'content-preview-container';
        previewContainer.innerHTML = '<h6>Preview:</h6><div class="preview-content border p-3 rounded"></div>';
        this.container.appendChild(previewContainer);
    }

    initSortable() {
        if (typeof Sortable !== 'undefined') {
            new Sortable(document.getElementById('content-blocks-container'), {
                animation: 150,
                ghostClass: 'sortable-ghost',
                onEnd: () => {
                    this.updateBlockOrder();
                    this.updatePreview();
                }
            });
        }
    }

    bindEvents() {
        // Add block buttons
        this.container.addEventListener('click', (e) => {
            if (e.target.classList.contains('add-block') || e.target.closest('.add-block')) {
                const button = e.target.classList.contains('add-block') ? e.target : e.target.closest('.add-block');
                const type = button.dataset.type;
                this.addBlock(type);
            }

            // Remove block
            if (e.target.classList.contains('remove-block') || e.target.closest('.remove-block')) {
                const button = e.target.classList.contains('remove-block') ? e.target : e.target.closest('.remove-block');
                const blockId = button.dataset.blockId;
                this.removeBlock(blockId);
            }

            // Toggle preview
            if (e.target.classList.contains('toggle-preview') || e.target.closest('.toggle-preview')) {
                this.togglePreview();
            }
        });

        // Block content changes
        this.container.addEventListener('input', (e) => {
            if (e.target.closest('.content-block')) {
                this.updatePreview();
            }
        });

        // Image upload handling
        this.container.addEventListener('change', (e) => {
            if (e.target.type === 'file' && e.target.classList.contains('image-upload')) {
                this.handleImageUpload(e.target);
            }
        });
    }

    addBlock(type) {
        if (this.blocks.length >= this.options.maxBlocks) {
            alert(`Maximum ${this.options.maxBlocks} blocks allowed`);
            return;
        }

        const blockId = `block_${++this.blockCounter}`;
        const block = {
            id: blockId,
            type: type,
            order: this.blocks.length,
            data: this.getDefaultBlockData(type)
        };

        this.blocks.push(block);
        this.renderBlock(block);
        this.updatePreview();
    }

    getDefaultBlockData(type) {
        switch (type) {
            case 'text':
                return { content: '' };
            case 'image':
                return { image_path: '', caption: '', alt_text: '', alignment: 'center' };
            case 'link':
                return { url: '', title: '', description: '' };
            case 'divider':
                return {};
            default:
                return {};
        }
    }

    renderBlock(block) {
        const container = document.getElementById('content-blocks-container');
        const blockElement = document.createElement('div');
        blockElement.className = 'content-block mb-3 p-3 border rounded';
        blockElement.dataset.blockId = block.id;
        blockElement.innerHTML = this.getBlockHTML(block);
        container.appendChild(blockElement);
    }

    getBlockHTML(block) {
        const commonHeader = `
            <div class="d-flex justify-content-between align-items-center mb-2">
                <h6 class="mb-0"><i class="fas fa-grip-vertical me-2"></i>${this.getBlockTypeLabel(block.type)}</h6>
                <button type="button" class="btn btn-sm btn-outline-danger remove-block" data-block-id="${block.id}">
                    <i class="fas fa-trash"></i>
                </button>
            </div>
        `;

        switch (block.type) {
            case 'text':
                return commonHeader + `
                    <div class="block-content">
                        <textarea class="form-control block-text-content" rows="4" placeholder="Enter your text content...">${block.data.content}</textarea>
                    </div>
                `;

            case 'image':
                return commonHeader + `
                    <div class="block-content">
                        <div class="row">
                            <div class="col-md-6">
                                <label class="form-label">Image</label>
                                <input type="file" class="form-control image-upload" accept="image/*" data-block-id="${block.id}">
                                ${block.data.image_path ? `<img src="${block.data.image_path}" class="img-thumbnail mt-2" style="max-height: 100px;">` : ''}
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Caption</label>
                                <input type="text" class="form-control block-image-caption" placeholder="Image caption..." value="${block.data.caption}">
                                <label class="form-label mt-2">Alt Text</label>
                                <input type="text" class="form-control block-image-alt" placeholder="Alt text..." value="${block.data.alt_text}">
                                <label class="form-label mt-2">Alignment</label>
                                <select class="form-select block-image-alignment">
                                    <option value="left" ${block.data.alignment === 'left' ? 'selected' : ''}>Left</option>
                                    <option value="center" ${block.data.alignment === 'center' ? 'selected' : ''}>Center</option>
                                    <option value="right" ${block.data.alignment === 'right' ? 'selected' : ''}>Right</option>
                                </select>
                            </div>
                        </div>
                    </div>
                `;

            case 'link':
                return commonHeader + `
                    <div class="block-content">
                        <div class="mb-2">
                            <label class="form-label">URL</label>
                            <input type="url" class="form-control block-link-url" placeholder="https://..." value="${block.data.url}">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Title</label>
                            <input type="text" class="form-control block-link-title" placeholder="Link title..." value="${block.data.title}">
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Description</label>
                            <textarea class="form-control block-link-description" rows="2" placeholder="Link description...">${block.data.description}</textarea>
                        </div>
                    </div>
                `;

            case 'divider':
                return commonHeader + `
                    <div class="block-content">
                        <hr class="my-2">
                        <small class="text-muted">This will create a horizontal divider line</small>
                    </div>
                `;

            default:
                return commonHeader + '<div class="block-content">Unknown block type</div>';
        }
    }

    getBlockTypeLabel(type) {
        const labels = {
            text: 'Text Block',
            image: 'Image Block',
            link: 'Link Block',
            divider: 'Divider Block'
        };
        return labels[type] || 'Unknown Block';
    }

    removeBlock(blockId) {
        if (confirm('Are you sure you want to remove this block?')) {
            // Remove from blocks array
            this.blocks = this.blocks.filter(block => block.id !== blockId);

            // Remove from DOM
            const blockElement = document.querySelector(`[data-block-id="${blockId}"]`);
            if (blockElement) {
                blockElement.remove();
            }

            this.updateBlockOrder();
            this.updatePreview();
        }
    }

    updateBlockOrder() {
        const blockElements = document.querySelectorAll('#content-blocks-container .content-block');
        blockElements.forEach((element, index) => {
            const blockId = element.dataset.blockId;
            const block = this.blocks.find(b => b.id === blockId);
            if (block) {
                block.order = index;
            }
        });

        // Sort blocks by order
        this.blocks.sort((a, b) => a.order - b.order);
    }

    handleImageUpload(input) {
        const blockId = input.dataset.blockId;
        const file = input.files[0];

        if (file) {
            // Store the file reference for form submission
            const block = this.blocks.find(b => b.id === blockId);
            if (block) {
                block.data.file = file;
                block.data.image_name = file.name;

                // Create preview URL
                const reader = new FileReader();
                reader.onload = (e) => {
                    block.data.preview_url = e.target.result;

                    // Update preview in block
                    const blockElement = document.querySelector(`[data-block-id="${blockId}"]`);
                    const existingImg = blockElement.querySelector('img');
                    if (existingImg) {
                        existingImg.src = e.target.result;
                    } else {
                        const imgElement = document.createElement('img');
                        imgElement.src = e.target.result;
                        imgElement.className = 'img-thumbnail mt-2';
                        imgElement.style.maxHeight = '100px';
                        input.parentNode.appendChild(imgElement);
                    }

                    this.updatePreview();
                };
                reader.readAsDataURL(file);
            }
        }
    }

    updatePreview() {
        const previewContent = document.querySelector('.preview-content');
        if (!previewContent) return;

        // Update block data from form inputs
        this.updateBlockDataFromInputs();

        let html = '';
        this.blocks.forEach(block => {
            html += this.renderBlockPreview(block);
        });

        previewContent.innerHTML = html || '<p class="text-muted">No content blocks added yet.</p>';
    }

    updateBlockDataFromInputs() {
        this.blocks.forEach(block => {
            const blockElement = document.querySelector(`[data-block-id="${block.id}"]`);
            if (!blockElement) return;

            switch (block.type) {
                case 'text':
                    const textContent = blockElement.querySelector('.block-text-content');
                    if (textContent) block.data.content = textContent.value;
                    break;

                case 'image':
                    const caption = blockElement.querySelector('.block-image-caption');
                    const altText = blockElement.querySelector('.block-image-alt');
                    const alignment = blockElement.querySelector('.block-image-alignment');
                    if (caption) block.data.caption = caption.value;
                    if (altText) block.data.alt_text = altText.value;
                    if (alignment) block.data.alignment = alignment.value;
                    break;

                case 'link':
                    const url = blockElement.querySelector('.block-link-url');
                    const title = blockElement.querySelector('.block-link-title');
                    const description = blockElement.querySelector('.block-link-description');
                    if (url) block.data.url = url.value;
                    if (title) block.data.title = title.value;
                    if (description) block.data.description = description.value;
                    break;
            }
        });
    }

    renderBlockPreview(block) {
        switch (block.type) {
            case 'text':
                return `<div class="content-block text-block mb-3">${block.data.content.replace(/\n/g, '<br>')}</div>`;

            case 'image':
                const imageUrl = block.data.preview_url || block.data.image_path;
                if (!imageUrl) return '';
                const caption = block.data.caption ? `<figcaption class="figure-caption">${block.data.caption}</figcaption>` : '';
                return `<figure class="content-block image-block text-${block.data.alignment} mb-3">
                    <img src="${imageUrl}" class="img-fluid" alt="${block.data.alt_text}" style="max-width: 100%;">
                    ${caption}
                </figure>`;

            case 'link':
                if (!block.data.url) return '';
                return `<div class="content-block link-block mb-3">
                    <div class="card">
                        <div class="card-body">
                            <h6 class="card-title"><a href="${block.data.url}" target="_blank" rel="noopener">${block.data.title || block.data.url}</a></h6>
                            ${block.data.description ? `<p class="card-text">${block.data.description}</p>` : ''}
                            <small class="text-muted">${block.data.url}</small>
                        </div>
                    </div>
                </div>`;

            case 'divider':
                return '<div class="content-block divider-block mb-3"><hr></div>';

            default:
                return '';
        }
    }

    togglePreview() {
        const previewContainer = document.getElementById('content-preview-container');
        const button = document.querySelector('.toggle-preview');

        if (previewContainer.classList.contains('d-none')) {
            previewContainer.classList.remove('d-none');
            button.innerHTML = '<i class="fas fa-eye-slash"></i> Hide Preview';
            this.updatePreview();
        } else {
            previewContainer.classList.add('d-none');
            button.innerHTML = '<i class="fas fa-eye"></i> Preview';
        }
    }

    getContentData() {
        this.updateBlockDataFromInputs();
        return JSON.stringify(this.blocks);
    }

    // Method to prepare form data with files for submission
    prepareFormData(formData) {
        this.updateBlockDataFromInputs();

        // Add dynamic content JSON (without file objects)
        const contentForSubmission = this.blocks.map(block => {
            const blockCopy = { ...block };
            if (block.type === 'image' && block.data.file) {
                // Remove file object from JSON, keep only metadata
                blockCopy.data = {
                    caption: block.data.caption || '',
                    alt_text: block.data.alt_text || '',
                    alignment: block.data.alignment || 'center',
                    image_name: block.data.image_name || ''
                };
            }
            return blockCopy;
        });

        formData.append('dynamic_content', JSON.stringify(contentForSubmission));

        // Add image files separately
        this.blocks.forEach(block => {
            if (block.type === 'image' && block.data.file) {
                formData.append(`content_images[${block.id}]`, block.data.file);
            }
        });

        return formData;
    }

    loadContent(contentData) {
        if (typeof contentData === 'string') {
            try {
                contentData = JSON.parse(contentData);
            } catch (e) {
                console.error('Invalid content data:', e);
                return;
            }
        }

        if (Array.isArray(contentData)) {
            // Process blocks to fix image paths
            this.blocks = contentData.map(block => {
                if (block.type === 'image' && block.data && block.data.image_path) {
                    // If image_path doesn't start with http, construct full URL
                    if (!block.data.image_path.startsWith('http')) {
                        block.data.image_path = window.location.origin + '/storage/images/berita/content/' + block.data.image_path;
                    }
                }
                return block;
            });

            this.blockCounter = Math.max(...this.blocks.map(b => parseInt(b.id.replace('block_', '')) || 0), 0);

            // Clear container
            const container = document.getElementById('content-blocks-container');
            container.innerHTML = '';

            // Render blocks
            this.blocks.forEach(block => {
                this.renderBlock(block);
            });

            this.updatePreview();
        }
    }

    clear() {
        this.blocks = [];
        this.blockCounter = 0;
        document.getElementById('content-blocks-container').innerHTML = '';
        this.updatePreview();
    }
}

// Global function to initialize content builder
window.initDynamicContentBuilder = function (containerId, options = {}) {
    return new DynamicContentBuilder(containerId, options);
};

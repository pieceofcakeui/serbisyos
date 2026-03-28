document.addEventListener('DOMContentLoaded', function() {
    let galleryImages = window.galleryImages || [];
    
    const galleryGrid = document.getElementById('galleryGrid');
    const floatingViewer = document.getElementById('floatingImageViewer');
    const floatingImage = document.getElementById('floatingViewImage');
    const closeButton = document.getElementById('closeFloatingViewer');
    const downloadButton = document.getElementById('downloadFloatingImage');

    const deleteConfirmModalEl = document.getElementById('deleteConfirmModal');
    const deleteConfirmModal = new bootstrap.Modal(deleteConfirmModalEl);
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

    function scrollToGallery() {
        const gallerySection = document.getElementById('galleryHeader');
        if (gallerySection) {
            setTimeout(() => {
                gallerySection.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }, 100);
        }
    }

    if (window.location.hash === '#galleryHeader') {
        scrollToGallery();
        history.pushState("", document.title, window.location.pathname + window.location.search);
    }

    function showFloatingViewer(src) {
        floatingImage.src = src;
        floatingViewer.classList.remove('d-none');
        setTimeout(() => {
            floatingViewer.classList.add('show');
        }, 10);
        document.body.style.overflow = 'hidden';
    }

    function hideFloatingViewer() {
        floatingViewer.classList.remove('show');
        setTimeout(() => {
            floatingViewer.classList.add('d-none');
            document.body.style.overflow = '';
            scrollToGallery();
        }, 300);
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && !floatingViewer.classList.contains('d-none')) {
            hideFloatingViewer();
        }
    });

    floatingViewer.addEventListener('click', function(e) {
        if (e.target === this) {
            hideFloatingViewer();
        }
    });

    closeButton.addEventListener('click', function(e) {
        e.stopPropagation();
        hideFloatingViewer();
    });

    downloadButton.addEventListener('click', function(e) {
        e.stopPropagation();
        if (floatingImage && floatingImage.src) {
            const link = document.createElement('a');
            link.href = floatingImage.src;
            link.download = 'gallery-image-' + new Date().getTime() + '.jpg';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    });

    function renderGallery() {
        galleryGrid.innerHTML = '';
        if (galleryImages && galleryImages.length > 0) {
            galleryImages.forEach((src, index) => {
                const container = document.createElement('div');
                container.className = 'gallery-item-container';

                const img = document.createElement('img');
                img.src = src;
                img.alt = `Gallery image ${index + 1}`;
                img.onerror = function() {
                    this.src = 'https://placehold.co/600x400/e9ecef/6c757d?text=Image+Not+Found';
                    this.style.objectFit = 'contain';
                    this.style.padding = '1rem';
                };

                const overlay = document.createElement('div');
                overlay.className = 'gallery-item-overlay';

                const viewIcon = document.createElement('i');
                viewIcon.className = 'fas fa-eye gallery-view-icon';

                const deleteButton = document.createElement('button');
                deleteButton.className = 'btn btn-danger btn-sm position-absolute top-0 end-0 m-2';
                deleteButton.innerHTML = '<i class="fas fa-trash"></i>';
                deleteButton.title = 'Delete Image';
                deleteButton.style.zIndex = "10";

                deleteButton.addEventListener('click', (e) => {
                    e.stopPropagation();
                    confirmDeleteBtn.dataset.index = index;
                    confirmDeleteBtn.dataset.src = src;
                    deleteConfirmModal.show();
                });

                overlay.appendChild(viewIcon);
                container.appendChild(img);
                container.appendChild(overlay);
                container.appendChild(deleteButton);

                container.addEventListener('click', () => showFloatingViewer(src));

                galleryGrid.appendChild(container);
            });
        } else {
            galleryGrid.innerHTML = '<p class="text-center text-muted w-100" style: text-align:center;"></p>';
        }
    }

    confirmDeleteBtn.addEventListener('click', function() {
        const index = parseInt(this.dataset.index, 10);
        const src = this.dataset.src;
        deleteGalleryImage(index, src);
    });

    function deleteGalleryImage(index, imagePath) {
        deleteConfirmModal.hide();
        fetch('../account/backend/photos.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `action=delete&image_path=${encodeURIComponent(imagePath)}`
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    toastr.success('Image deleted successfully.');
                    galleryImages = data.images;
                    renderGallery();
                    scrollToGallery();
                } else {
                    toastr.error(data.message || 'Failed to delete image.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                toastr.error('An error occurred while deleting the image.');
            });
    }

    renderGallery();

    const uploadGalleryModalEl = document.getElementById('uploadGalleryModal');
    const uploadGalleryBtn = document.getElementById('uploadGalleryBtn');
    const galleryImageInput = document.getElementById('galleryImageInput');
    const imagePreviewContainer = document.getElementById('imagePreviewContainer');
    const dropZone = document.getElementById('drop-zone');
    let tempFiles = [];
    const MAX_GALLERY_FILES = 3;
    const MAX_SIZE_MB = 5;

    if (uploadGalleryModalEl) {
        const uploadGalleryModal = new bootstrap.Modal(uploadGalleryModalEl);

        uploadGalleryModalEl.addEventListener('hidden.bs.modal', function() {
            tempFiles = [];
            imagePreviewContainer.innerHTML = '';
            galleryImageInput.value = '';
        });

        dropZone.addEventListener('dragover', e => {
            e.preventDefault();
            dropZone.classList.add('drag-over');
        });
        dropZone.addEventListener('dragleave', () => dropZone.classList.remove('drag-over'));
        dropZone.addEventListener('drop', e => {
            e.preventDefault();
            dropZone.classList.remove('drag-over');
            handleFiles(e.dataTransfer.files);
        });

        galleryImageInput.addEventListener('change', function() {
            handleFiles(this.files);
        });
        uploadGalleryBtn.addEventListener('click', uploadImages);

        function handleFiles(files) {
            const newFiles = Array.from(files);
            if (tempFiles.length + newFiles.length > MAX_GALLERY_FILES) {
                toastr.error(`You can upload a maximum of ${MAX_GALLERY_FILES} images.`);
                return;
            }
            newFiles.forEach(file => {
                if (!file.type.startsWith('image/')) {
                    toastr.warning(`File "${file.name}" is not an image and was skipped.`);
                    return;
                }
                if (file.size > MAX_SIZE_MB * 1024 * 1024) {
                    toastr.warning(`File "${file.name}" exceeds ${MAX_SIZE_MB}MB size limit and was skipped.`);
                    return;
                }
                if (tempFiles.length < MAX_GALLERY_FILES) {
                    tempFiles.push(file);
                    createPreview(file);
                }
            });
        }

        function createPreview(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewWrapper = document.createElement('div');
                previewWrapper.className = 'preview-image-wrapper';
                const removeBtn = document.createElement('button');
                removeBtn.className = 'btn btn-sm btn-danger position-absolute top-0 end-0 p-1';
                removeBtn.innerHTML = '<i class="fas fa-times"></i>';
                removeBtn.onclick = function() {
                    tempFiles = tempFiles.filter(f => f !== file);
                    previewWrapper.remove();
                };
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'preview-image';
                previewWrapper.appendChild(img);
                previewWrapper.appendChild(removeBtn);
                imagePreviewContainer.appendChild(previewWrapper);
            };
            reader.readAsDataURL(file);
        }

        function uploadImages() {
            if (tempFiles.length === 0) {
                toastr.error('Please select at least one image to upload.');
                return;
            }
            const formData = new FormData();
            formData.append('action', 'upload');
            tempFiles.forEach(file => formData.append('images[]', file));
            fetch('../account/backend/photos.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        toastr.success('Images uploaded successfully.');
                        uploadGalleryModal.hide();

                        if (data.images) {
                            galleryImages = data.images;
                            renderGallery();
                        }

                        uploadGalleryModalEl.addEventListener('hidden.bs.modal', function onModalHidden() {
                            scrollToGallery();
                            uploadGalleryModalEl.removeEventListener('hidden.bs.modal', onModalHidden);
                        }, {
                            once: true
                        });
                    } else {
                        toastr.error(data.message || 'Failed to upload images.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    toastr.error('An error occurred while uploading images.');
                });
        }
    }
});
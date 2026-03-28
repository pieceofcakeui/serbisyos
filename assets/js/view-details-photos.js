document.addEventListener('DOMContentLoaded', function() {
    const galleryImages = window.galleryData?.images || [];
    const galleryGrid = document.getElementById('galleryGrid');

    if (!galleryGrid) return;

    renderGalleryItems(galleryImages, galleryGrid);
});

function renderGalleryItems(images, container) {
    if (!container) return;
    
    container.innerHTML = '';
    
    if (!images || images.length === 0) {
        return;
    }
    
    images.forEach((src, index) => {
        const item = createGalleryItem(src, index);
        if (item) {
            container.appendChild(item);
        }
    });
}

function createGalleryItem(src, index) {
    const container = document.createElement('div');
    container.className = 'view-shop-gallery-item'; 
    container.style.cursor = 'pointer'; 
    
    const img = document.createElement('img');
    img.src = src;
    img.alt = `Gallery image ${index + 1}`;
    img.className = 'img-fluid'; 
    img.onerror = handleImageError;
    
    container.addEventListener('click', () => {
        openImageModal(src, `Gallery image ${index + 1}`);
    });
    
    container.appendChild(img);
    
    return container;
}

function handleImageError() {
    this.src = 'https://placehold.co/600x400/e9ecef/6c757d?text=Image+Not+Found';
    this.style.objectFit = 'contain';
    this.style.padding = '1rem';
}


function openImageModal(imageSrc, imageTitle) {
    const modal = document.getElementById('imageModal');
    const modalImage = document.getElementById('view-shop-modalImage');
    
    if (imageSrc && imageSrc !== '') {
        modalImage.src = imageSrc;
        modalImage.alt = imageTitle || 'Gallery Image';
        
        const bootstrapModal = new bootstrap.Modal(modal);
        bootstrapModal.show();
    }
}
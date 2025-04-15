// Sample data structure
const contentData = {
    hero: {
        images: [
            { id: 1, url: 'hero1.jpg', title: 'Hero Image 1' },
            { id: 2, url: 'hero2.jpg', title: 'Hero Image 2' }
        ]
    },
    categories: {
        uniform: {
            images: [
                { id: 1, url: 'uniform1.jpg', title: 'Uniform 1' },
                { id: 2, url: 'uniform2.jpg', title: 'Uniform 2' }
            ]
        },
        accessories: {
            images: [
                { id: 1, url: 'accessory1.jpg', title: 'Accessory 1' },
                { id: 2, url: 'accessory2.jpg', title: 'Accessory 2' }
            ]
        },
        shirt: {
            images: [
                { id: 1, url: 'shirt1.jpg', title: 'Shirt 1' },
                { id: 2, url: 'shirt2.jpg', title: 'Shirt 2' }
            ]
        }
    },
    featured: {
        products: [
            { id: 1, url: 'product1.jpg', title: 'Featured Product 1' },
            { id: 2, url: 'product2.jpg', title: 'Featured Product 2' }
        ]
    }
};

// Initialize the page
document.addEventListener('DOMContentLoaded', function() {
    loadAllSections();
});

function loadAllSections() {
    loadHeroSection();
    loadCategoriesSection();
    loadFeaturedSection();
}

function openSection(sectionId) {
    // Close all sections first
    document.querySelectorAll('.section-content').forEach(section => {
        section.classList.remove('active');
    });
    
    // Open the selected section
    const section = document.getElementById(`${sectionId}-content`);
    if (section) {
        section.classList.add('active');
    }
}

function openCategory(categoryId) {
    // Handle category click
    console.log(`Opening category: ${categoryId}`);
    // Add your category-specific logic here
}

function loadHeroSection() {
    const heroGrid = document.getElementById('hero-image-grid');
    if (!heroGrid) return;

    heroGrid.innerHTML = contentData.hero.images.map(image => `
        <div class="image-item">
            <img src="${image.url}" alt="${image.title}">
            <div class="image-controls">
                <button onclick="editImage('hero', ${image.id})">
                    <i class="material-icons">edit</i>
                </button>
                <button onclick="deleteImage('hero', ${image.id})">
                    <i class="material-icons">delete</i>
                </button>
            </div>
        </div>
    `).join('');
}

function loadCategoriesSection() {
    // Load STI Uniform
    const uniformGrid = document.getElementById('uniform-images');
    if (uniformGrid) {
        uniformGrid.innerHTML = contentData.categories.uniform.images.map(image => `
            <div class="image-item">
                <img src="${image.url}" alt="${image.title}">
                <div class="image-controls">
                    <button onclick="editImage('uniform', ${image.id})">
                        <i class="material-icons">edit</i>
                    </button>
                    <button onclick="deleteImage('uniform', ${image.id})">
                        <i class="material-icons">delete</i>
                    </button>
                </div>
            </div>
        `).join('');
    }

    // Load STI Accessories
    const accessoriesGrid = document.getElementById('accessories-images');
    if (accessoriesGrid) {
        accessoriesGrid.innerHTML = contentData.categories.accessories.images.map(image => `
            <div class="image-item">
                <img src="${image.url}" alt="${image.title}">
                <div class="image-controls">
                    <button onclick="editImage('accessories', ${image.id})">
                        <i class="material-icons">edit</i>
                    </button>
                    <button onclick="deleteImage('accessories', ${image.id})">
                        <i class="material-icons">delete</i>
                    </button>
                </div>
            </div>
        `).join('');
    }

    // Load STI Shirt
    const shirtGrid = document.getElementById('shirt-images');
    if (shirtGrid) {
        shirtGrid.innerHTML = contentData.categories.shirt.images.map(image => `
            <div class="image-item">
                <img src="${image.url}" alt="${image.title}">
                <div class="image-controls">
                    <button onclick="editImage('shirt', ${image.id})">
                        <i class="material-icons">edit</i>
                    </button>
                    <button onclick="deleteImage('shirt', ${image.id})">
                        <i class="material-icons">delete</i>
                    </button>
                </div>
            </div>
        `).join('');
    }
}

function loadFeaturedSection() {
    const featuredGrid = document.getElementById('featured-products-grid');
    if (!featuredGrid) return;

    featuredGrid.innerHTML = contentData.featured.products.map(product => `
        <div class="featured-item">
            <img src="${product.url}" alt="${product.title}">
            <h3>${product.title}</h3>
            <div class="product-controls">
                <button onclick="editProduct(${product.id})">
                    <i class="material-icons">edit</i>
                </button>
                <button onclick="deleteProduct(${product.id})">
                    <i class="material-icons">delete</i>
                </button>
            </div>
        </div>
    `).join('');
}

function editImage(section, imageId) {
    // Handle image editing
    console.log(`Editing image ${imageId} in ${section}`);
    // Add your image editing logic here
}

function deleteImage(section, imageId) {
    // Handle image deletion
    console.log(`Deleting image ${imageId} from ${section}`);
    // Add your image deletion logic here
}

function editProduct(productId) {
    // Handle product editing
    console.log(`Editing product ${productId}`);
    // Add your product editing logic here
}

function deleteProduct(productId) {
    // Handle product deletion
    console.log(`Deleting product ${productId}`);
    // Add your product deletion logic here
}

// Sample content data
const contentItems = [
    {
        id: 1,
        type: 'banner',
        title: 'Welcome Banner 2024',
        description: 'Main banner for student portal',
        image: 'banner1.jpg',
        startDate: '2024-01-01',
        endDate: '2024-12-31'
    },
    {
        id: 2,
        type: 'announcement',
        title: 'New Library Hours',
        description: 'Updated library schedule',
        image: 'announcement1.jpg',
        startDate: '2024-02-01',
        endDate: '2024-03-01'
    }
    // Add more items as needed
];

// Initialize content grid
document.addEventListener('DOMContentLoaded', function() {
    loadContentGrid();
    initializeDropZone();
});

function loadContentGrid() {
    const grid = document.querySelector('.content-grid');
    grid.innerHTML = '';

    contentItems.forEach(item => {
        const card = createContentCard(item);
        grid.appendChild(card);
    });
}

function createContentCard(item) {
    const div = document.createElement('div');
    div.className = 'content-card';
    
    div.innerHTML = `
        <div class="content-image">
            <img src="${item.image}" alt="${item.title}">
            <div class="content-overlay">
                <button class="overlay-btn" onclick="editContent(${item.id})">
                    <i class="material-icons">edit</i>
                </button>
                <button class="overlay-btn" onclick="deleteContent(${item.id})">
                    <i class="material-icons">delete</i>
                </button>
            </div>
        </div>
        <div class="content-info">
            <div class="content-type">${item.type}</div>
            <h3 class="content-title">${item.title}</h3>
            <div class="content-dates">
                <span>From: ${formatDate(item.startDate)}</span>
                <span>To: ${formatDate(item.endDate)}</span>
            </div>
        </div>
    `;
    return div;
}

function initializeDropZone() {
    const dropZone = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');

    // Handle drag and drop
    dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('dragover');
    });

    dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('dragover');
    });

    dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');
        handleFiles(e.dataTransfer.files);
    });

    fileInput.addEventListener('change', (e) => {
        handleFiles(e.target.files);
    });
}

function handleFiles(files) {
    const previewArea = document.getElementById('previewArea');
    previewArea.innerHTML = '';

    Array.from(files).forEach(file => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const preview = document.createElement('div');
                preview.className = 'preview-item';
                preview.innerHTML = `
                    <img src="${e.target.result}" alt="Preview">
                    <button class="remove-btn" onclick="this.parentElement.remove()">
                        <i class="material-icons">close</i>
                    </button>
                `;
                previewArea.appendChild(preview);
            };
            reader.readAsDataURL(file);
        }
    });
}

function openUploadModal() {
    document.getElementById('uploadModal').style.display = 'block';
}

function closeUploadModal() {
    document.getElementById('uploadModal').style.display = 'none';
    document.getElementById('uploadForm').reset();
    document.getElementById('previewArea').innerHTML = '';
}

function editContent(id) {
    const content = contentItems.find(item => item.id === id);
    if (content) {
        openUploadModal();
        // Populate form with content data
        // This would be implemented based on your needs
    }
}

function deleteContent(id) {
    if (confirm('Are you sure you want to delete this content?')) {
        // Handle deletion
        showNotification('Content deleted successfully');
    }
}

// Form submission
document.getElementById('uploadForm').addEventListener('submit', function(e) {
    e.preventDefault();
    // Handle form submission
    showNotification('Content uploaded successfully');
    closeUploadModal();
});

// Utility functions
function formatDate(dateString) {
    return new Date(dateString).toLocaleDateString();
}

function showNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.textContent = message;
    document.body.appendChild(notification);
    
    notification.style.display = 'block';
    setTimeout(() => {
        notification.style.display = 'none';
        notification.remove();
    }, 3000);
}

// Filter content based on type
document.getElementById('contentType').addEventListener('change', function(e) {
    const type = e.target.value;
    const grid = document.querySelector('.content-grid');
    grid.innerHTML = '';

    const filteredItems = type === 'all' 
        ? contentItems 
        : contentItems.filter(item => item.type === type);

    filteredItems.forEach(item => {
        const card = createContentCard(item);
        grid.appendChild(card);
    });
}); 
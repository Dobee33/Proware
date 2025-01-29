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
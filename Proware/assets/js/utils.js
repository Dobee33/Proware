// Common utility functions used across different JS files
const utils = {
    formatDate(dateString) {
        return new Date(dateString).toLocaleDateString();
    },
    
    showNotification(message) {
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
}; 
function logout() {
    // Redirect to logout.php
    window.location.href = '../Pages/login.php';
}

function clearActivities() {
    if (confirm('Are you sure you want to clear all activities?')) {
        fetch('../PAMO PAGES/clear_activities.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'

            }
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    document.querySelector('.activity-list').innerHTML =
                        "<p class='no-activities'>No recent activities</p>";
                } else {
                    alert('Failed to clear activities: ' + (data.error || 'Unknown error'));
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while clearing activities');
            });
    }
}
// Notification Bar Logic
const notificationIcon = document.getElementById('notification-icon');
const notificationBar = document.getElementById('notification-bar');

notificationIcon.addEventListener('click', () => {
    if (notificationBar.style.right === '0px') {
        notificationBar.style.right = '-300px';
        document.getElementById('notification-dot').style.display = 'none';
    } else {
        notificationBar.style.right = '0px';
        document.getElementById('notification-dot').style.display = 'none';
    }
});

// Close notification bar when clicking outside
document.addEventListener('click', (e) => {
    if (!notificationBar.contains(e.target) && !notificationIcon.contains(e.target)) {
        notificationBar.style.right = '-450px';
    }
});

function closeNotificationBar() {
    document.getElementById("notification-bar").style.right = "-450px";
}

function displayLoading() {
    // Show the loading overlay and indicator
    const loadingOverlay = document.getElementById('loadingOverlay');
    loadingOverlay.style.display = 'block';
}


function closeLoading() {
    // Show the loading overlay and indicator
    const loadingOverlay = document.getElementById('loadingOverlay');
    loadingOverlay.style.display = 'none';
}

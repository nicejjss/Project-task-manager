// Notification Bar Logic
const notificationIcon = document.getElementById('notification-icon');
const notificationBar = document.getElementById('notification-bar');
const sidebar = document.getElementById('sidebar');

notificationIcon.addEventListener('click', () => {
    if (notificationBar.style.right === '0px') {
        notificationBar.style.right = '-300px';
    } else {
        notificationBar.style.right = '0px';
    }
});

// Close notification bar when clicking outside
document.addEventListener('click', (e) => {
    if (!notificationBar.contains(e.target) && !notificationIcon.contains(e.target)) {
        notificationBar.style.right = '-450px';
    }
});

// Sidebar Toggle Logic for Mobile
const menuToggle = document.getElementById('hamburger-icon');
const hamburgerIcon = document.getElementById('hamburger');
const closeIcon = document.getElementById('close');
menuToggle.addEventListener('click', () => {
    sidebar.classList.toggle('open');
    if (sidebar.classList.contains('open')) {
        hamburgerIcon.style = "display: none";
        closeIcon.style = "display: block";
    } else {
        hamburgerIcon.style = "display: block";
        closeIcon.style = "display: none";
    }
});

function closeNotificationBar() {
    document.getElementById("notification-bar").style.right = "-450px";
}

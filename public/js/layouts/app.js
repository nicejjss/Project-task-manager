// Notification Bar Logic
const notificationIcon = document.getElementById('notification-icon');
const notificationBar = document.getElementById('notification-bar');

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
const sidebar = document.getElementById('sidebar');
const menuToggle = document.createElement('div');
menuToggle.classList.add('menu-toggle');
menuToggle.innerHTML = '☰';
document.querySelector('.logo-section').appendChild(menuToggle);

menuToggle.addEventListener('click', () => {
    sidebar.classList.toggle('open');
});

function closeNotificationBar() {
    document.getElementById("notification-bar").style.right = "-450px";
}

document.getElementById('toggleButton').addEventListener('click', function() {
    var emailList = document.getElementById('emailList');
    if (emailList.style.display === 'none') {
        emailList.style.display = 'block';
        this.innerHTML = 'Đóng <i class="fa-solid fa-angle-up"></i>';
    } else {
        emailList.style.display = 'none';
        this.innerHTML = 'Thành Viên <i class="fa-solid fa-angle-down"></i>';
    }
});

document.addEventListener("DOMContentLoaded", function() {
    var flashMessage = document.getElementById("flash-message");
    if (flashMessage) {
        flashMessage.style.position = "fixed";
        flashMessage.style.top = "20px";
        flashMessage.style.right = "20px";
        flashMessage.style.zIndex = "9999";
        flashMessage.style.display = "block";

        setTimeout(function() {
            flashMessage.classList.remove("show-flash-message");
            flashMessage.classList.add("hide-flash-message");
        }, 1500); // 1.5 seconds
    }
});

document.getElementById('inviteForm').addEventListener('submit', function(event) {
    event.preventDefault(); // Prevent the default form submission

    var xhr = new XMLHttpRequest();
    var url = this.action;
    var formData = new FormData(this);

    xhr.open('POST', url, true);
    xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
    xhr.setRequestHeader('X-CSRF-TOKEN', document.querySelector('input[name="_token"]').value);

    xhr.onload = function() {
        if (xhr.status === 200) {
            if (xhr.responseText !== 'Gửi mail thất bại') {
                showNotification(xhr.responseText, 'success');
                document.getElementById('inviteForm').reset(); // Clear the form
            } else {
                showNotification(xhr.responseText, 'error');
            }
        } else {
            showNotification(xhr.responseText, 'error');
        }
    };

    xhr.onerror = function() {
        showNotification('An error occurred during the request.', 'error');
    };

    xhr.send(formData);
});

function showNotification(message, type) {
    var notification = document.getElementById('notification-mail');
    notification.textContent = message;
    notification.className = 'notification'; // Reset the class
    if (type === 'error') {
        notification.classList.add('error');
    }
    notification.style.display = 'block';

    // Hide the notification after 3 seconds
    setTimeout(function() {
        notification.style.display = 'none';
    }, 3000);
}

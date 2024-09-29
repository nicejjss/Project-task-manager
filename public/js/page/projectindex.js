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

function toggleMemberList() {
    var emailDropdown = document.getElementById("member-email-dropdown");
    emailDropdown.style.display = emailDropdown.style.display === "none" ? "block" : "none";
}

function filterMemberEmails() {
    var input, filter, emailList, li, span, i, txtValue;
    input = document.getElementById("memberSearchInput");
    filter = input.value.toUpperCase();
    emailList = document.getElementById("memberList");
    li = emailList.getElementsByTagName("li");

    for (i = 0; i < li.length; i++) {
        span = li[i].getElementsByClassName("member-email")[0];
        txtValue = span.textContent || span.innerText;
        if (txtValue.toUpperCase().includes(filter)) {
            li[i].style.display = "";
        } else {
            li[i].style.display = "none";
        }
    }
}

// Optional: Close the dropdown if the user clicks outside of it
window.onclick = function(event) {
    if (
        !event.target.matches('.member-dropdown-btn') &&
        !event.target.matches('#member-email-dropdown') &&
        !event.target.closest('#member-email-dropdown')
    ) {
        var emailDropdown = document.getElementById("member-email-dropdown");
        if (emailDropdown.style.display === "block") {
            emailDropdown.style.display = "none";
        }
    }
}

// Show the modal with a custom message
function showModal(message) {
    const modal = document.getElementById('modal');
    const modalMessage = document.getElementById('modalMessage');
    const closeBtn = document.getElementsByClassName('close')[0];

    modalMessage.textContent = message;
    modal.style.display = 'block';

    closeBtn.onclick = function() {
        modal.style.display = 'none';
    };

    window.onclick = function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    };
}

// Avatar change functionality
document.getElementById('changeAvatarBtn').addEventListener('click', function() {
    document.getElementById('avatarInput').click();
});

document.getElementById('avatarInput').addEventListener('change', function(event) {
    const file = event.target.files[0];
    const errorMsg = document.getElementById('errorMsg');

    // Allowed image formats
    const validFormats = ['image/jpeg', 'image/png'];

    if (file && validFormats.includes(file.type)) {
        errorMsg.textContent = ''; // Clear any previous error message
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('avatar').src = e.target.result; // Preview the image
        };
        reader.readAsDataURL(file);
    } else {
        errorMsg.textContent = 'Upload ảnh đúng định dạng (JPEG/PNG).';
    }
});

// Save button functionality
document.getElementById('saveBtn').addEventListener('click', function (e) {
    e.preventDefault(); // Prevent the default form submission

    // Show the loading overlay and indicator
    const loadingOverlay = document.getElementById('loadingOverlay');
    loadingOverlay.style.display = 'block';

    // Create FormData object
    var formData = new FormData();

    // Append user information
    var name = document.getElementById('name').value;
    var email = document.getElementById('email').value;

    formData.append('name', name);
    formData.append('email', email);

    if (name === '' || email === '') {
        showModal('Vui lòng điền đầy đủ thông tin');
        return; // Exit the function if any required field is empty
    }

    // Check if avatar was selected and append it to FormData
    var avatarInput = document.getElementById('avatarInput');
    formData.append('avatar', avatarInput.files[0]);

    // Send the data via AJAX
    fetch('/user/update', {
        method: 'POST',
        body: formData,
        headers: {
            "X-CSRF-Token": document.querySelector('input[name=_token]').value
        },
    })
        .then(response => {
            return response.json().then(data => {
                if (response.ok) {
                    showToast(1, 'Cập nhật thành công');
                } else {
                    showToast(2, data);
                }
            });
        })
        .catch(error => {
            alert(error);
            showModal('Có lỗi xảy ra');
        })
        .finally(() => {
            // Hide the loading overlay when the request completes
            loadingOverlay.style.display = 'none';
        });
});

// Change password functionality
document.getElementById('changePasswordBtn').addEventListener('click', function() {
    window.location.href = '/user/change_password'; // Redirect to password reset page
});

// Cancel button functionality
document.getElementById('cancelBtn').addEventListener('click', function() {
    window.history.back(); // Redirect back to the previous page
});

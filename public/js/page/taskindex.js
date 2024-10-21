let selectedUsers = [];

// Show user list on input focus
document.getElementById('userSearch').addEventListener('focus', function () {
    document.getElementById('userResults').style.display = 'block';
});

// Search for users
document.getElementById('userSearch').addEventListener('input', function () {
    const query = this.value.toLowerCase();
    const results = Array.from(document.querySelectorAll('.user-item'));

    results.forEach(item => {
        if (item.textContent.toLowerCase().includes(query)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });

    const visibleItems = results.filter(item => item.style.display === 'block');
    document.getElementById('userResults').style.display = visibleItems.length > 0 ? 'block' : 'none';
});

// Add selected user to the notified list
document.getElementById('userResults').addEventListener('click', function (e) {
    if (e.target.classList.contains('user-item')) {
        const userId = e.target.getAttribute('data-user-id');
        const userName = e.target.getAttribute('data-user-name');

        if (!selectedUsers.find(user => user.id === userId)) {
            selectedUsers.push({id: userId, name: userName});
            document.getElementById('selectedUsers').insertAdjacentHTML('beforeend', `
                        <span class="selected-user" data-user-id="${userId}">${userName} <span class="close-user" data-user-id="${userId}">&times;</span></span>
                    `);
            document.getElementById('userSearch').value = '';
            updateNotifiedUsers();
        }
    }
});

// Remove user from the selected list
document.getElementById('selectedUsers').addEventListener('click', function (e) {
    if (e.target.classList.contains('close-user')) {
        const userId = e.target.getAttribute('data-user-id');
        selectedUsers = selectedUsers.filter(user => user.id !== userId);
        e.target.parentElement.remove();
        updateNotifiedUsers();
    }
});

// Update the hidden input with selected users
function updateNotifiedUsers() {
    document.getElementById('notifiedUsers').value = JSON.stringify(selectedUsers);
}

// Hide user results dropdown when clicking outside
document.addEventListener('click', function (e) {
    const searchInput = document.getElementById('userSearch');
    const userResults = document.getElementById('userResults');
    if (!searchInput.contains(e.target) && !userResults.contains(e.target)) {
        userResults.style.display = 'none';
    }
});

document.querySelectorAll('.delete-cross').forEach(function (deleteButton) {
    deleteButton.addEventListener('click', function (event) {
        event.preventDefault(); // Prevent default <a> behavior

        // Confirmation dialog
        if (confirm('Bạn có muốn xóa file này không, hành động này không thể hoàn tác')) {
            // Remove the <li> immediately
            const listItem = this.closest('.file-item');
            listItem.remove();

            // Get the delete URL from data attribute
            const deleteUrl = this.getAttribute('data-delete-url');

            // Send the delete request (using fetch)
            fetch(deleteUrl, {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json'
                },
            })
                .then(response => {
                    if (!response.ok) {
                        alert('Failed to delete the file.');
                        document.querySelector('#file-list').appendChild(listItem);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the file.');
                    // Optionally, re-add the <li> if an error occurs
                    document.querySelector('#file-list').appendChild(listItem);
                });
        }
    });
});

const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// Form submission event listener
document.getElementById('commentForm').addEventListener('submit', function (e) {
    e.preventDefault();

    // Show the loading overlay and indicator
    const loadingOverlay = document.getElementById('loadingOverlay');
    loadingOverlay.style.display = 'block';

    // Get the comment text and selected users
    const comment = document.getElementById('commentText').value;
    const projectId = document.getElementById('projectId').value;
    const taskId = document.getElementById('taskId').value;
    const notifiedUsers = selectedUsers.map(user => user.id);

    // Validate if the comment is not empty
    if (!comment) {
        alert('Vui lòng nhập bình luận trước khi gửi.');
        return;
    }

    // Prepare form data
    const formData = new FormData();
    formData.append('comment', comment);
    notifiedUsers.forEach(user => {
        formData.append('userNotified[]', user);
    });

    // Send AJAX request to Laravel
    fetch('/project/' + projectId + '/task/' + taskId + '/comment/create', { // You can also use the Laravel route helper for dynamic routes
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken,
        },
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (data) {
                showToast(1, 'Thêm comment thành công')

                // Dynamically insert the new comment
                const commentContainer = document.createElement('div');
                commentContainer.classList.add('comment', 'container');
                commentContainer.innerHTML = `
                    <div class="comment-own">
                       <img class="avatar-app" src="${members[data.user_id]['avatar'] ? members[data.user_id]['avatar'] : '/avatar.png'}" alt="${members[data.user_id]['name']}">
                        <div class="comment-own-infor">
                            <h4 class="comment-own-infor-name">${members[data.user_id]['name']}</h4>
                            <p class="comment-own-infor-date">${formatDate(data.created_at)}</p>
                        </div>
                    </div>
                    <div class="comment-details">
                        <div class="notified-users">
                            <p class="notify-prefix">Thông báo tới:</p>
                            ${notifiedUsers.map(userNotify => `
                                <div class="notify-container">
                                    <img class="avatar-app avatar-app-notify" src="${members[userNotify]['avatar'] ? members[userNotify]['avatar'] : '/avatar.png'}" alt="${members[userNotify]['name']}">
                                    <div class="notify-name">${members[userNotify]['name']}</div>
                                </div>
                            `).join('')}
                        </div>
                        <p class="comment-content">${data.comment_text}</p>
                    </div>
                `;
                document.querySelector('.comments-section').appendChild(commentContainer);


                // Create the new log item element
                const logItem = document.createElement('div');
                logItem.classList.add('log-item');

                logItem.innerHTML = `
        <div class="comment-own">
            <img class="avatar-app" src="${members[data.user_id]['avatar'] ? members[data.user_id]['avatar'] : '/avatar.png'}" alt="${members[data.user_id]['name']}">
            <div class="comment-own-infor">
                <p class="comment-own-infor-name">${members[data.user_id]['name']}</p>
                <p class="comment-own-infor-date">${formatDate(data.created_at)}</p>
            </div>
        </div>
        <p class="history-description">
            Thêm bình luận
        </p>
    `;

                // Append the new log item to the history section
                document.getElementById('log-list').appendChild(logItem); // Replace 'historySection' with your actual container ID

                // Clear form after submission
                document.getElementById('commentText').value = '';
                selectedUsers = [];
                document.getElementById('selectedUsers').innerHTML = '';
                updateNotifiedUsers();
            } else {
                showToast(2, 'Có lỗi xảy ra, thử lại')
            }
        })
        .catch(error => console.error('Error:', error))
        .finally(() => {
            loadingOverlay.style.display = 'none';
        });
});


function formatDate(date) {
    date = new Date(date);
    const year = date.getFullYear();
    const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-indexed
    const day = String(date.getDate()).padStart(2, '0');
    const hours = String(date.getHours()).padStart(2, '0');
    const minutes = String(date.getMinutes()).padStart(2, '0');
    const seconds = String(date.getSeconds()).padStart(2, '0');

    return `${year}-${month}-${day} ${hours}:${minutes}:${seconds}`;
}

function uploadFile(input) {
    const form = document.getElementById('fileUploadForm');
    const formData = new FormData(form);

    // Display loading indicator (optional)
    displayLoading();

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value
        }
    })
        .then(response => response.json())
        .then(data => {
            // Hide loading indicator
            hideLoading();

            // Check if the file upload was successful
            if (data.success) {
                // Display the file data in the frontend
                displayFileData(data.file);
            } else {
                console.error("File upload failed:", data.message);
            }
        })
        .catch(error => {
            hideLoading();
            console.error("Error during file upload:", error);
        });
}

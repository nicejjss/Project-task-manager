document.addEventListener('DOMContentLoaded', function () {
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

    // Handle form submission
    document.getElementById('commentForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const comment = document.getElementById('commentText').value;
        const notifiedUsers = selectedUsers.map(user => user.name).join(', ');

        console.log('Comment:', comment);
        console.log('Notified Users:', notifiedUsers);

        // Clear form after submission
        document.getElementById('commentText').value = '';
        selectedUsers = [];
        document.getElementById('selectedUsers').innerHTML = '';
        updateNotifiedUsers();
    });

    // Hide user results dropdown when clicking outside
    document.addEventListener('click', function (e) {
        const searchInput = document.getElementById('userSearch');
        const userResults = document.getElementById('userResults');
        if (!searchInput.contains(e.target) && !userResults.contains(e.target)) {
            userResults.style.display = 'none';
        }
    });
});

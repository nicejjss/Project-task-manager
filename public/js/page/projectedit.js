$(document).ready(function() {
    $('#editor').summernote({
        height: 450, // Set initial height
        maxHeight: 450, // Set maximum height
        toolbar: [
            ['style', ['style']], // Adding the style button
            ['font', ['bold', 'italic', 'underline', 'clear']],
            ['fontname', ['fontname']],
            ['insert', ['link']], // Add link toolbar
            ['fontsize', ['fontsize']],
            ['color', ['color']],
            ['para', ['ul', 'ol', 'paragraph']],
        ],
        styleTags: [
            'p',
            { title: 'Blockquote', tag: 'blockquote', className: 'blockquote', value: 'blockquote' },
            'pre', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6'
        ]
    });
});

// Array to hold invited people
// let invitedPeople = [];

// Handle Add Person button click
document.getElementById('addPersonBtn').addEventListener('click', function() {
    const inviteEmail = document.getElementById('inviteEmail').value;
    var emailError = document.getElementById('emailError');
    var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (inviteEmail && emailRegex.test(inviteEmail)) {
        // Check if email already exists in invitedPeople array
        if (!invitedPeople.includes(inviteEmail)) {
            invitedPeople.push(inviteEmail);

            // Update the UI to show the invited person
            const invitedList = document.getElementById('invitedList');
            const newPerson = document.createElement('li');
            newPerson.innerHTML = `${inviteEmail} <span style="color: red;
                                                               cursor: pointer;
                                                               position: absolute;
                                                               right: 40px;"
                                                               class="delete-icon"
                                                               data-email="${inviteEmail}">
                                                               X</span>`;
            invitedList.appendChild(newPerson);

            // Clear input field
            document.getElementById('inviteEmail').value = '';
            emailError.style.display = 'none';
        } else {
            emailError.textContent = 'Đã thêm email này vào danh sách mời';
            emailError.style.display = 'block';
        }
    } else {
        // alert('Please enter a valid email address.');
        emailError.textContent = 'Thêm Email đúng định dạng';
        emailError.style.display = 'block';
    }
});

// Handle form submission
document.getElementById('projectForm').addEventListener('submit', function(event) {
    event.preventDefault();

    // Show the loading overlay and indicator
    const loadingOverlay = document.getElementById('loadingOverlay');
    loadingOverlay.style.display = 'block';

    const projectName = document.getElementById('projectName').value;
    const projectID = document.getElementById('projectID').value;
    const content = $('#editor').summernote('code');

    // Create a Blob object with the Markdown content
    const markdownBlob = new Blob([content], { type: 'text/html' });

    // Create a FormData object to hold the form data
    const formData = new FormData();
    formData.append('description', markdownBlob, `${projectName}.html`);
    formData.append('name', projectName);
    formData.append('people', JSON.stringify(invitedPeople));
    formData.append('projectID', projectID);

    // Post the FormData object (replace URL with your actual endpoint)
    fetch('http://127.0.0.1:8000/project/{{$projectId}}/edit', {
        method: 'POST',
        headers: {
            "X-CSRF-Token": document.querySelector('input[name=_token]').value
        },
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            showToast(1, 'Cập nhật thành công');
            window.location.href = "http://127.0.0.1:8000/project/" + data;
        })
        .catch((error) => {
            showToast(2, 'Cập nhật thất bại');
            console.error('Error:', error);
        })
        .finally(() => {
            // Hide the loading overlay when the request completes
            loadingOverlay.style.display = 'none';
        });
});



let emailToDelete = null; // Variable to store the email to delete

// Event listener for the delete icon
document.getElementById('invitedList').addEventListener('click', function(event) {
    if (event.target.classList.contains('delete-icon')) {
        emailToDelete = event.target.getAttribute('data-email'); // Store the email to delete
        // Show the custom modal
        document.getElementById('deleteModal').style.display = 'flex';
    }
});

// Handle the Confirm button click
document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
    if (emailToDelete) {
        // Remove the corresponding list item from the DOM
        const emailItem = document.querySelector(`.delete-icon[data-email="${emailToDelete}"]`).closest('li');
        if (emailItem) {
            // Remove the email from invitedPeople array
            invitedPeople = invitedPeople.filter(email => email !== emailToDelete);
            // Remove the list item from the UI
            emailItem.remove();
        }

        // Hide the modal
        document.getElementById('deleteModal').style.display = 'none';
        emailToDelete = null; // Reset the variable
    }
});

// Handle the Cancel button click
document.getElementById('cancelDeleteBtn').addEventListener('click', function() {
    // Hide the modal without deleting anything
    document.getElementById('deleteModal').style.display = 'none';
    emailToDelete = null; // Reset the variable
});

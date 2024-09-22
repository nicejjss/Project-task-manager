// Initialize Quill editor for project description
var quill = new Quill('#editor', {
    modules: {
        toolbar: [
            [{ header: [1, 2, false] }],
            ["bold", "italic", "underline"],
            [{ 'list' : 'ordered' }, { 'list' : 'bullet' }],
            ['link', 'blockquote']
        ]
    },
    placeholder: "Mô tả...",
    theme: 'snow',
});

// Initialize showdown.js converter for HTML to Markdown conversion
const converter = new showdown.Converter();

// Array to hold invited people
let invitedPeople = [];

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

// Handle delete icon click event
document.getElementById('invitedList').addEventListener('click', function(event) {
    if (event.target.classList.contains('delete-icon')) {
        const email = event.target.getAttribute('data-email');
        const invitedList = document.getElementById('invitedList');
        const emailItem = event.target.closest('li');

        // Remove the email from invitedPeople array
        invitedPeople = invitedPeople.filter(e => e !== email);

        // Remove the list item from the UI
        invitedList.removeChild(emailItem);
    }
});

// Handle form submission
document.getElementById('projectForm').addEventListener('submit', function(event) {
    event.preventDefault();

    // Show the loading overlay and indicator
    const loadingOverlay = document.getElementById('loadingOverlay');
    loadingOverlay.style.display = 'block';

    const projectName = document.getElementById('projectName').value;
    const projectDescriptionHTML = quill.root.innerHTML;
    const projectDescriptionMD = converter.makeMarkdown(projectDescriptionHTML);

    // Create a Blob object with the Markdown content
    const markdownBlob = new Blob([projectDescriptionMD], { type: 'text/markdown' });

    // Create a FormData object to hold the form data
    const formData = new FormData();
    formData.append('description', markdownBlob, `${projectName}.md`);
    formData.append('name', projectName);
    formData.append('people', JSON.stringify(invitedPeople));

    // Post the FormData object (replace URL with your actual endpoint)
    fetch('http://127.0.0.1:8000/project/store', {
        method: 'POST',
        headers: {
            "X-CSRF-Token": document.querySelector('input[name=_token]').value
        },
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            window.location.href = "http://127.0.0.1:8000/project/" + data;
        })
        .catch((error) => {
            console.error('Error:', error);
        })
        .finally(() => {
            // Hide the loading overlay when the request completes
            loadingOverlay.style.display = 'none';
        });
});

// Initialize Quill editor for project description
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

let selectedFiles = []; // Global array to store selected files

// Trigger hidden file input when button is clicked
document.getElementById('addFilesBtn').addEventListener('click', function() {
    document.getElementById('fileInput').click();
});

// Handle file selection and display the selected files
document.getElementById('fileInput').addEventListener('change', function() {
    const fileList = document.getElementById('fileList');

    // Loop through selected files and add them to selectedFiles array
    for (const file of this.files) {
        selectedFiles.push(file);

        // Create a new list item for each file
        const li = document.createElement('li');
        li.textContent = file.name;

        // Create a delete icon and add to list item
        const deleteIcon = document.createElement('span');
        deleteIcon.textContent = '❌';
        deleteIcon.style.cursor = 'pointer';
        deleteIcon.style.marginLeft = '10px';

        // Remove file from selectedFiles when delete icon is clicked
        deleteIcon.addEventListener('click', function() {
            const fileIndex = selectedFiles.indexOf(file);
            if (fileIndex > -1) {
                selectedFiles.splice(fileIndex, 1); // Remove the file from selectedFiles array
            }
            fileList.removeChild(li); // Remove the list item
        });

        li.appendChild(deleteIcon);
        fileList.appendChild(li);
    }

    // Clear the file input after files are processed
    this.value = '';
});

// Handle form submission with file attachments
document.getElementById('taskForm').addEventListener('submit', function(event) {
    event.preventDefault();

    const projectName = document.getElementById('title').value.trim();
    const projectDescriptionHTML = $('#editor').summernote('code');
    const user = document.getElementById('user').value;
    const priority = document.getElementById('priority').value;
    const taskType = document.getElementById('tasktype').value;
    const deadline = document.getElementById('deadline').value;

    let parent = 0;
    if (hasParent) {
        parent = document.getElementById('parent').value;
    }

    // Clear previous errors from the existing error section
    clearErrors();

    // Validate the form
    const errors = validateForm();

    // If there are errors, display them in the existing error section
    if (errors.length > 0) {
        displayErrors(errors);
        return; // Stop form submission if validation fails
    }

    // Show the loading overlay and indicator
    const loadingOverlay = document.getElementById('loadingOverlay');
    loadingOverlay.style.display = 'flex';

    const markdownBlob = new Blob([projectDescriptionHTML], { type: 'text/html' });

    // Create a FormData object to hold the form data
    const formData = new FormData();
    formData.append('description', markdownBlob, `description.html`);
    formData.append('title', projectName);
    formData.append('assignee', user);
    formData.append('priority', priority);
    formData.append('tasktype', taskType);
    formData.append('deadline', deadline);
    formData.append('parent', parent);

    let fileErrors = false; // Flag to track if there are file errors
    const allowedExtensions = ['pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'txt', 'jpg', 'jpeg', 'png', 'gif', 'svg', 'json', 'xml', 'csv'];
    // Handle file attachments (if any)
    selectedFiles.forEach((file, index) => {
        const fileExtension = file.name.split('.').pop().toLowerCase(); // Get file extension

        // Validate file extension
        if (allowedExtensions.includes(fileExtension)) {
            formData.append('attachments[]', file); // Append each file to FormData
        } else {
            let errors = [`Chỉ chấp nhận các tệp đính kèm: ${allowedExtensions.join(', ')}`]
            displayErrors(errors);
            fileErrors = true;
        }
    });

    // If there were file errors, stop form submission
    if (fileErrors) {
        loadingOverlay.style.display = 'none';
        return; // Stop form submission if validation fails
    }

    // Post the FormData object (replace URL with your actual endpoint)
    fetch(`http://127.0.0.1:8000/project/${projectId}/task/create`, {
        method: 'POST',
        headers: {
            "X-CSRF-Token": document.querySelector('input[name=_token]').value,
            "Accept": "application/json"
        },
        body: formData
    })
        .then(response => response.json())
        .then(data => {
            if (Number.isInteger(data)) {
                showToast(1, 'Tạo thành công')
                window.location.href = "http://127.0.0.1:8000/project/" + projectId + '/task/' + data;
            } else {
                let errors = ['Lỗi không xác định'];
                displayErrors(errors);
            }
        })
        .catch((error) => {
            showToast(2, 'Có lỗi xảy ra')
        })
        .finally(() => {
            // Hide the loading overlay when the request completes
            loadingOverlay.style.display = 'none';
        });
});

// Function to clear previous errors
function clearErrors() {
    const errorList = document.querySelector('.error ul');
    if (errorList) {
        document.getElementById('error').display = 'none';
        errorList.innerHTML = '';
    }
}

// Function to validate the form and return an array of errors
function validateForm() {
    const projectName = document.getElementById('title').value.trim();
    const user = document.getElementById('user').value;
    const priority = document.getElementById('priority').value;

    let errors = []; // Array to store error messages

    // Required field checks
    if (!projectName) {
        errors.push("Cần Điền Tên Công Việc.");
    }
    if (!user) {
        errors.push("Cần Có Người Thực Hiện.");
    }
    if (!priority) {
        errors.push("Cần Điền Độ Ưu Tiên.");
    }

    return errors; // Return the array of errors
}

// Function to display errors in the existing error section
function displayErrors(errors) {
    const errorList = document.querySelector('.error ul');
    if (errorList) {
        errors.forEach(error => {
            const li = document.createElement('li');
            li.textContent = error;
            errorList.appendChild(li); // Append each error to the existing list
        });

        scrollToError()
    } else {
        // If there's no error section (which shouldn't happen), you can create one
        const errorDiv = document.createElement('div');
        errorDiv.className = 'error';
        errorDiv.id = 'error';
        const ul = document.createElement('ul');
        ul.style.paddingLeft = '10px';
        errors.forEach(error => {
            const li = document.createElement('li');
            li.textContent = error;
            ul.appendChild(li);
        });
        errorDiv.appendChild(ul);
        document.querySelector('.container').insertBefore(errorDiv, document.querySelector('.container').firstChild);

        scrollToError()
    }
}

function scrollToError() {
    // Scroll to the top of the page
    window.scrollTo({
        top: 0,
        behavior: 'smooth' // Smooth scrolling to the top
    });
}

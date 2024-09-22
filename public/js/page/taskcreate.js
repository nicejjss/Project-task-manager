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


// Handle form submission
document.getElementById('taskForm').addEventListener('submit', function(event) {
    event.preventDefault();

    // Show the loading overlay and indicator
    const loadingOverlay = document.getElementById('loadingOverlay');
    loadingOverlay.style.display = 'block';

    const projectName = document.getElementById('title').value;
    const projectDescriptionHTML = quill.root.innerHTML;
    const projectDescriptionMD = converter.makeMarkdown(projectDescriptionHTML);

    // Create a Blob object with the Markdown content
    const markdownBlob = new Blob([projectDescriptionMD], { type: 'text/markdown' });

    // Create a FormData object to hold the form data
    const formData = new FormData();
    formData.append('description', markdownBlob, `${projectName}.md`);
    formData.append('name', projectName);

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

function previewFiles() {
    var preview = document.querySelector('#fileList');
    var files = document.querySelector('input[type=file]').files;

    preview.innerHTML = '';
    for (var i = 0; i < files.length; i++) {
        var li = document.createElement('li');
        var span = document.createElement('span');
        span.textContent = 'x';
        span.style.color = 'red';
        span.style.cursor = 'pointer';
        span.addEventListener('click', function(e) {
            var fileIndex = Array.prototype.indexOf.call(files, e.target.parentNode.file);
            files = Array.from(files).filter((_, index) => index !== fileIndex);
            e.target.parentNode.remove();

            // Check if there are no files left and clear the input field
            if (files.length === 0) {
                document.querySelector('input[type=file]').value = '';
            }
        });

        li.textContent = files[i].name;
        li.file = files[i];
        li.appendChild(span);
        preview.appendChild(li);
    }
}

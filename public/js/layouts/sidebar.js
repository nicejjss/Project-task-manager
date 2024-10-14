function toggleDropdown() {
    var dropdownContent = document.getElementById("dropdown-content");
    dropdownContent.classList.toggle("show");
}

function filterProjects() {
    var input, filter, projectList, a, i, txtValue;
    input = document.getElementById("searchInput");
    filter = input.value.toUpperCase();
    projectList = document.getElementById("projectList");
    a = projectList.getElementsByTagName("a");
    for (i = 0; i < a.length; i++) {
        txtValue = a[i].textContent || a[i].innerText;
        if (txtValue.toUpperCase().includes(filter)) {
            a[i].style.display = "";
        } else {
            a[i].style.display = "none";
        }
    }
}

// Close the dropdown if the user clicks outside of it, except when clicking on the search box
window.onclick = function(event) {
    if (
        !event.target.matches('.dropbtn') &&
        !event.target.matches('#dropdown-content') &&
        !event.target.closest('#dropdown-content')) {
        var dropdowns = document.getElementById("dropdown-content");
        if (dropdowns.classList.contains('show')) {
            dropdowns.classList.remove('show');
        }
    }
}

const sidebar = document.getElementById('sidebar');
// Sidebar Toggle Logic for Mobile
const menuToggle = document.getElementById('hamburger-icon');
const hamburgerIcon = document.getElementById('hamburger');
const closeIcon = document.getElementById('close');
menuToggle.addEventListener('click', () => {
    sidebar.classList.toggle('open-side');
    if (sidebar.classList.contains('open-side')) {
        hamburgerIcon.style = "display: none";
        closeIcon.style = "display: block";
    } else {
        hamburgerIcon.style = "display: block";
        closeIcon.style = "display: none";
    }
});

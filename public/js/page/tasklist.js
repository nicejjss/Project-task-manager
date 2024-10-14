// Add click event for selecting items from the dropdown
document.querySelectorAll('.member-item').forEach(item => {
    item.addEventListener('click', function() {
        const parentDropdown = item.closest('.member-dropdown-content');
        const selectedValue = item.getAttribute('data-value');
        const button = parentDropdown.previousElementSibling;
        button.querySelector('span').innerText = selectedValue; // Update button text with selected value
        parentDropdown.style.display = 'none'; // Close dropdown after selection
    });
});

// Function to toggle the filter section
function toggleFilterSection() {
    const filterSection = document.getElementById('filter-section');
    filterSection.style.display = filterSection.style.display === 'flex' ? 'none' : 'flex';
}

// Function to close the dropdown when clicking outside
document.addEventListener("click", function (event) {
    const dropdowns = document.querySelectorAll(".member-dropdown-content");
    const toggleButtons = document.querySelectorAll(".member-dropdown-btn");

    // Check if the clicked target is outside any dropdown and toggle buttons
    if (![...toggleButtons].some(btn => btn.contains(event.target)) &&
        ![...dropdowns].some(dropdown => dropdown.contains(event.target))) {
        dropdowns.forEach(dropdown => dropdown.style.display = "none"); // Close all dropdowns
    }
});

// Toggle dropdown display
function toggleDropdown(dropdownId) {
    var dropdown = document.getElementById(dropdownId);
    dropdown.style.display = (dropdown.style.display === 'none' || dropdown.style.display === '') ? 'block' : 'none';
}

// Filter function for dropdown items
function filterDropdown(dropdownId, searchValue) {
    var dropdown = document.getElementById(dropdownId);
    var filter = searchValue.toLowerCase();
    var items = dropdown.getElementsByTagName('li');

    // Loop through all list items, and hide those that don't match the search query
    for (var i = 0; i < items.length; i++) {
        var itemValue = items[i].getAttribute('data-value').toLowerCase();
        if (itemValue.indexOf(filter) > -1) {
            items[i].style.display = '';
        } else {
            items[i].style.display = 'none';
        }
    }
}

// Set input value and alias after selecting from the dropdown
function setInputValue(inputType, realValue, alias) {
    var inputSearch = document.getElementById(inputType);
    var inputHidden = document.getElementById(inputType + 'Hidden');
    var displaySpan = document.getElementById(inputType + 'Display');

    // Set the hidden input with the real value
    inputHidden.value = realValue;

    // Set the visible input and display the selected alias
    inputSearch.value = alias;
    displaySpan.innerHTML = alias;

    // Hide the dropdown after selection
    document.getElementById(inputType + 'Dropdown').style.display = 'none';

    console.log("Selected Alias: " + alias + " | Real Value: " + realValue);
}


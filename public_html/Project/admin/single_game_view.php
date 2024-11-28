<?php require(__DIR__ . "/../../../partials/nav.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Searchable Dropdown - Bootstrap 5.3.3</title>
  <!-- Bootstrap 5.3.3 CSS -->
  <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"> -->
  <style>
    .dropdown-menu {
      max-height: 200px; /* Limit the height of the dropdown */
      overflow-y: auto;  /* Enable scrolling */
    }
  </style>
</head>
<body>
  <div class="container mt-5">
    <h2>Searchable Dropdown</h2>
    <div class="dropdown">
      <!-- Searchable input field -->
      <input 
        type="text" 
        class="form-control" 
        id="searchInput" 
        placeholder="Type to search..." 
        data-bs-toggle="dropdown" 
        aria-expanded="false"
      />
      <ul class="dropdown-menu w-100" id="dropdownList">
        <li><a class="dropdown-item" href="#" data-value="None">None</a></li>
        <li><a class="dropdown-item" href="#" data-value="Option1">Option 1</a></li>
        <li><a class="dropdown-item" href="#" data-value="Option2">Option 2</a></li>
        <li><a class="dropdown-item" href="#" data-value="Option3">Option 3</a></li>
        <li><a class="dropdown-item" href="#" data-value="Option4">Option 4</a></li>
        <li><a class="dropdown-item" href="#" data-value="Option5">Option 5</a></li>
      </ul>
    </div>
    <p class="mt-3">Selected: <span id="selectedValue">None</span></p>
  </div>

  <!-- Bootstrap 5.3.3 JS Bundle (includes Popper.js) -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  
  <script>
    // Elements
    const searchInput = document.getElementById('searchInput');
    const dropdownList = document.getElementById('dropdownList');
    const selectedValueDisplay = document.getElementById('selectedValue');

    // Initialize the Bootstrap dropdown instance
    const dropdownInstance = new bootstrap.Dropdown(searchInput, { autoClose: false });

    // Filter dropdown items based on input
    searchInput.addEventListener('input', function () {
      const filter = searchInput.value.toLowerCase(); // Get the input text
      const items = dropdownList.querySelectorAll('.dropdown-item'); // Get all dropdown items

      items.forEach(item => {
        const text = item.textContent.toLowerCase();
        // Show or hide the item based on the filter
        if (text.includes(filter)) {
          item.style.display = 'block';
        } else {
          item.style.display = 'none';
        }
      });

      // Show the dropdown if input has focus and filter is applied
      dropdownInstance.show();
    });

    // Handle item selection
    dropdownList.addEventListener('click', function (e) {
      if (e.target.classList.contains('dropdown-item')) {
        e.preventDefault(); // Prevent default link behavior
        const selectedValue = e.target.getAttribute('data-value'); // Get the selected value
        const selectedText = e.target.textContent; // Get the text of the selected item

        // Display the selected value and update the input field
        selectedValueDisplay.textContent = selectedText;
        searchInput.value = selectedText;

        // Close the dropdown after selection
        dropdownInstance.hide();
      }
    });

    // Close dropdown on blur if the input loses focus
    searchInput.addEventListener('blur', function () {
      setTimeout(() => dropdownInstance.hide(), 150);
    });
  </script>
</body>
</html>

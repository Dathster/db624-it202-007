function flash(message = "", color = "info") {
    let flash = document.getElementById("flash");

    // Create a div wrapper for positioning
    let outerDiv = document.createElement("div");
    outerDiv.className = "row";

    // Create the toast container
    let toastContainer = document.createElement("div");
    toastContainer.className = 'toast-container position-fixed top-1 end-0';

    // Create the toast element
    let toast = document.createElement("div");
    toast.className = `toast bg-${color}`;
    toast.setAttribute('role', 'alert');
    toast.setAttribute('aria-live', 'assertive');
    toast.setAttribute('aria-atomic', 'true');

    // Create the toast header
    let toastHeader = document.createElement("div");
    toastHeader.className = "toast-header";

    let strong = document.createElement("strong");
    strong.className = "me-auto";
    strong.textContent = "Steamed Games";

    let button = document.createElement("button");
    button.type = "button";
    button.className = "btn-close";
    button.setAttribute("data-bs-dismiss", "toast");
    button.setAttribute("aria-label", "Close");

    toastHeader.appendChild(strong);
    toastHeader.appendChild(button);

    // Create the toast body
    let toastBody = document.createElement("div");
    toastBody.className = `toast-body`;
    toastBody.textContent = message;

    toast.appendChild(toastHeader);
    toast.appendChild(toastBody);
    toastContainer.appendChild(toast);
    outerDiv.appendChild(toastContainer);

    // Add the toast to the DOM
    flash.appendChild(outerDiv);

    // Initialize the toast with the correct options
    const toastInstance = new bootstrap.Toast(toast, {
        autohide: true,  // Enable autohide
        delay: 5000,     // Set delay time (5 seconds)
    });

    toastInstance.show();

    // Optional: Remove the toast container after it's hidden
    toast.addEventListener("hidden.bs.toast", () => {
        outerDiv.remove();
    });
}



//db624 it202-007 11/11/24
//Validate emails
function validate_email(email){
    let re = new RegExp('^([a-zA-Z0-9_-]+\.?)+@([a-zA-Z0-9_-]+\.)+([a-zA-Z0-9_-]+)$')
    let isValid = true;
    if(!re.test(email)){
        flash("[Client]: Email must follow user@domain.com format and contain only upper and lowercase letters, numbers, underscores, and hyphens", "warning");
        isValid = false;
    }

    return isValid;
}

//Validate usernames
function validate_username(usr){
    re = new RegExp('^[a-z0-9_-]{3,30}$');
    isValid = true;
    //Raise an error messsage if username doesn't follow rules
    if(!re.test(usr)){
        flash("[Client]: Username must be between 3 and 30 characters long and can only contain a-z, 0-9, _, -","warning");
        isValid = false;
    }
    return isValid;
}

//Validate passwords
function validate_password(pw){
    isValid = true;
    //Raise an error message if the password is shorter than 8 characters
    if(pw.length < 8){
        flash("[Client]: Password must be longer than 8 characters", "warning");
        isValid = false;
    }
    return isValid;
}
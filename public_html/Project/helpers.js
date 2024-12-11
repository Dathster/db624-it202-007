function flash(message = "", color = "info") {
    let flash = document.getElementById("flash");
    //create a div (or whatever wrapper we want)
    let outerDiv = document.createElement("div");
    outerDiv.className = "row justify-content-center";
    let innerDiv = document.createElement("div");

    //apply the CSS (these are bootstrap classes which we'll learn later)
    innerDiv.className = `alert alert-${color}`;
    //set the content
    innerDiv.innerText = message;

    outerDiv.appendChild(innerDiv);
    //add the element to the DOM (if we don't it merely exists in memory)
    flash.appendChild(outerDiv);
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
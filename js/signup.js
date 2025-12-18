/*  Munashe Madziwanyika - g23M8754
    Theodore Masi - g23M7028
    Kyle Nkomo - g23N8653
    Keith Dube - g23D5910 
*/

// TODO:
// Add return true in validateSignUp when all landing pages are done

function offline() {
    document.getElementById("network").innerHTML = "You must be online to submit this form.";
}

function showPassword() {
    let x = document.getElementById("password");
    let y = document.getElementById("show");
    if (x.type === "password") {
        x.type = "text";
        y.innerHTML = "Hide password"
    } else {
        x.type = "password";
        y.innerHTML = "Show password"
    }
}

function validateNames() {
    // Name: Only alphabetic characters
    let message = document.getElementById("message");
    let namePattern = /^[A-Za-z]+$/;
    let firstname = document.getElementById("firstname").value;
    let lastname = document.getElementById("lastname").value;
    if (firstname.length > 0 && !namePattern.test(firstname)) {
        // document.getElementById("signup-btn").disabled = true;
        alert("First name must contain only alphabetic characters.");
    }
    if (lastname.length > 0 && !namePattern.test(lastname)) {
        // document.getElementById("signup-btn").disabled = true;
        alert("Last name must contain only alphabetic characters.");
    }
}

function validateNumber() {
    // Phone number validation
    let phone = document.getElementById("phone").value;
    let digitPattern = /^[0-9+ ]+$/;
    if (!digitPattern.test(phone)) {
        // Check if phone number contains only digits, +, or spaces
        // document.getElementById("signup-btn").disabled = true;
        alert("Phone number must contain only digits.");
    }
    if (phone.length == 10) {
        // If a number is 10 digits long, check if phone number starts with 0
        if (phone.substring(0, 1) != '0') {
            // document.getElementById("signup-btn").disabled = true;
            alert("Phone number must start with 0 or country code.");
        }
    } else if (phone.length > 10) {
        // If a number is more than 10 digits long, check if phone number starts with +
        if (phone.substring(0, 1) != '+') {
            // document.getElementById("signup-btn").disabled = true;
            alert("Phone number must start with 0 or country code.");
        }
    } else {
        // document.getElementById("signup-btn").disabled = true;
        alert("Phone number must be at least 10 characters long.");
    }
}

function validateSignUp() {
    // Regular expression patterns
    // Password validation
    let password = document.getElementById("password");
    let confirmPassword = document.getElementById("confirmPassword");
    // Check if passwords match
    if (password.value !== confirmPassword.value) {
        // document.getElementById("signup-btn").disabled = true;
        alert("Passwords do not match.");
        return false;
    }

    /* Check for at least one uppercase letter, 
       one special character, 
       one digit, 
       and minimum length of 8
    */
    let uppercasePattern = /[A-Z]/;
    if (!uppercasePattern.test(password.value)) {
        // document.getElementById("signup-btn").disabled = true;
        alert("Password must contain at least one uppercase letter.");
        return false;
    }

    // Check if the password contains special characters
    let specialCharPattern = /[!@#$%^&*(),.?":{}|<>]/;
    let digitPattern = /\d/;
    if (!specialCharPattern.test(password.value) || !digitPattern.test(password.value)) {
        // document.getElementById("signup-btn").disabled = true;
        alert("Password must contain at least one special character and one digit.");
        return false;
    }
    if (password.value.length < 8) {
        // document.getElementById("signup-btn").disabled = true;
        alert("Password must be at least 8 characters long.");
        return false;
    }

    window.location.href = "signup.php"; // Redirect to homepage or desired page
    return true; // Prevent form submission for demonstration purposes
}
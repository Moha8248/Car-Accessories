const wrapper = document.querySelector(".wrapper");
const registerLink = document.querySelector(".register-link");
const loginLink = document.querySelector(".login-link");

registerLink.onclick = () => {
  wrapper.classList.add("active");
};

loginLink.onclick = () => {
  wrapper.classList.remove("active");
};
// Function to validate login form
function validateLoginForm() {
  const email = document.querySelector('.login input[name="email"]').value;
  const password = document.querySelector(
    '.login input[name="password"]'
  ).value;

  if (!validateEmail(email)) {
    alert("Please enter a valid email address.");
    return false;
  }

  if (password.length < 6) {
    alert("Password must be at least 6 characters long.");
    return false;
  }

  return true; // Allow form submission if all validations pass
}

// Function to validate signup form
function validateSignupForm() {
  const username = document.querySelector(
    '.register input[name="username"]'
  ).value;
  const email = document.querySelector('.register input[name="email"]').value;
  const password = document.querySelector(
    '.register input[name="password"]'
  ).value;

  if (username.length < 3) {
    alert("Username must be at least 3 characters long.");
    return false;
  }

  if (!validateEmail(email)) {
    alert("Please enter a valid email address.");
    return false;
  }

  if (password.length < 6) {
    alert("Password must be at least 6 characters long.");
    return false;
  }

  return true; // Allow form submission if all validations pass
}

// Helper function to validate email using regex
function validateEmail(email) {
  const re = /^[a-zA-Z0-9._-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/;
  return re.test(String(email).toLowerCase());
}

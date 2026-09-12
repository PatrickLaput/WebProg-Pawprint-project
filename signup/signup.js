document.addEventListener("DOMContentLoaded", function () {

    const signupForm = document.getElementById("signup-form");
    const password = document.getElementById("password");
    const confirmPassword = document.getElementById("confirm-password");
    const passwordError = document.getElementById("password-error");

    signupForm.addEventListener("submit", function (event) {

        // Clear previous message
        passwordError.textContent = "";
        confirmPassword.classList.remove("password-mismatch");

        // Check if passwords match
        if (password.value !== confirmPassword.value) {

            event.preventDefault();

            passwordError.textContent = "Passwords do not match.";
            passwordError.classList.add("password-error");

            confirmPassword.classList.add("password-mismatch");
        }
    });

});

document.addEventListener("DOMContentLoaded", function () {

    const passwordToggles = document.querySelectorAll(".password-toggle");

    passwordToggles.forEach(function (button) {

        button.addEventListener("click", function () {

            const input = this.closest(".signup-input").querySelector("input");

            if (input.type === "password") {
                input.type = "text";
                this.textContent = "👁";
            } else {
                input.type = "password";
                this.textContent = "👁";
            }

        });

    });

});
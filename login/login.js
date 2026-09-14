document.addEventListener("DOMContentLoaded", function () {

    const passwordToggles = document.querySelectorAll(".password-toggle");

    passwordToggles.forEach(function (button) {

        button.addEventListener("click", function () {

            const input = this.closest(".login-input").querySelector("input");

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

document.addEventListener("DOMContentLoaded", function () {
    const loginForm = document.getElementById("loginForm");
    const loginError = document.getElementById("loginError");

    if (!loginForm || !loginError) {
        return;
    }

    loginForm.addEventListener("submit", async function (event) {
        event.preventDefault();

        // Clear the previous error message
        loginError.textContent = "";

        const email = loginForm.querySelector('input[name="email"]').value.trim();
        const password = loginForm.querySelector('input[name="password"]').value.trim();

        // Check if the fields are empty
        if (email === "" || password === "") {
            loginError.textContent = "Please enter your email and password.";
            return;
        }

        const submitButton = loginForm.querySelector('button[type="submit"]');

        if (submitButton) {
            submitButton.disabled = true;
        }

        try {
            const response = await fetch(loginForm.action, {
                method: "POST",
                body: new FormData(loginForm),
                headers: {
                    "Accept": "application/json"
                }
            });

            const data = await response.json();

            if (data.success) {
                window.location.href = data.redirect;
            } else {
                loginError.textContent =
                    data.message || "Incorrect email or password.";
            }
        } catch (error) {
            loginError.textContent =
                "Unable to log in right now. Please try again.";
        } finally {
            if (submitButton) {
                submitButton.disabled = false;
            }
        }
    });
});
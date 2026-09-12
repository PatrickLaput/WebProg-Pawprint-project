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
document.addEventListener("DOMContentLoaded", function () {

    const cartForms = document.querySelectorAll("form[action='add-to-cart.php']");

    cartForms.forEach(function (form) {

        form.addEventListener("submit", function (event) {

            event.preventDefault();

            const button = form.querySelector(".add-cart");

            const formData = new FormData(form);

            button.disabled = true;
            button.textContent = "Adding...";

            fetch("add-to-cart.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {

                if (data.success) {

                    button.textContent = "Added to Cart";

                    setTimeout(function () {
                        button.textContent = "Add to Cart";
                        button.disabled = false;
                    }, 1000);

                } else {

                    alert(data.message);

                    button.textContent = "Add to Cart";
                    button.disabled = false;
                }

            })
            .catch(error => {

                console.error(error);

                alert("Something went wrong while adding the product.");

                button.textContent = "Add to Cart";
                button.disabled = false;
            });

        });

    });

});
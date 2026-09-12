document.addEventListener("DOMContentLoaded", function () {

    const minPrice = document.getElementById("min-price");
    const maxPrice = document.getElementById("max-price");

    const minLabel = document.getElementById("min-price-label");
    const maxLabel = document.getElementById("max-price-label");


    if (!minPrice || !maxPrice) {
        return;
    }


    function updateSlider() {

        let min = parseInt(minPrice.value);
        let max = parseInt(maxPrice.value);


        /*
        Prevent minimum from going above maximum
        */

        if (min > max) {

            if (document.activeElement === minPrice) {
                min = max;
                minPrice.value = min;
            }

            else {
                max = min;
                maxPrice.value = max;
            }

        }


        minLabel.textContent =
            "₱" + min.toLocaleString();

        maxLabel.textContent =
            "₱" + max.toLocaleString();
    }


    minPrice.addEventListener(
        "input",
        updateSlider
    );

    maxPrice.addEventListener(
        "input",
        updateSlider
    );


    updateSlider();

});

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

document.addEventListener("DOMContentLoaded", function () {

    const searchToggle = document.querySelector(".search-toggle");
    const searchOverlay = document.getElementById("search-overlay");
    const searchInput = document.getElementById("search-input");
    const searchForm = document.getElementById("search-form");

    if (!searchToggle || !searchOverlay) {
        return;
    }

    /* Open search box */
    searchToggle.addEventListener("click", function (event) {

        event.preventDefault();

        searchOverlay.classList.add("active");

        setTimeout(function () {
            searchInput.focus();
        }, 100);

    });


    /* Close when clicking outside search box */
    searchOverlay.addEventListener("click", function (event) {

        if (event.target === searchOverlay) {
            searchOverlay.classList.remove("active");
        }

    });


    /* Close with Escape */
    document.addEventListener("keydown", function (event) {

        if (event.key === "Escape") {
            searchOverlay.classList.remove("active");
        }

    });


    /* Search */
    searchForm.addEventListener("submit", function (event) {

        event.preventDefault();

        const searchValue = searchInput.value.trim();

        if (searchValue === "") {
            return;
        }

        window.location.href =
            "shop.php?search=" +
            encodeURIComponent(searchValue);

    });

});
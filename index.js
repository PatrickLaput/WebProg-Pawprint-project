document.addEventListener("DOMContentLoaded", function () {

    const slides = document.querySelectorAll(".testimonial-slide");
    const dots = document.querySelectorAll(".testimonial-dot");

    dots.forEach(function (dot) {
        dot.addEventListener("click", function () {

            const slideIndex = Number(this.dataset.slide);

            slides.forEach(slide => {
                slide.classList.remove("active");
            });

            dots.forEach(dot => {
                dot.classList.remove("active");
            });

            slides[slideIndex].classList.add("active");
            this.classList.add("active");
        });
    });

});

document.addEventListener("DOMContentLoaded", function () {

    const cartForms = document.querySelectorAll(".bestseller-cart-form");

    cartForms.forEach(function (form) {

        form.addEventListener("submit", function (event) {

            event.preventDefault();

            const button = form.querySelector(".add-cart");
            const formData = new FormData(form);

            button.disabled = true;
            button.textContent = "Adding...";

            fetch("shop/add-to-cart.php", {
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
document.addEventListener("DOMContentLoaded", function () {

    /*
    |--------------------------------------------------------------------------
    | UPDATE QUANTITY
    |--------------------------------------------------------------------------
    */

    const quantityInputs = document.querySelectorAll(".quantity-input");

    quantityInputs.forEach(function (input) {

        input.addEventListener("change", function () {

            let quantity = parseInt(this.value);

            const cartId = this.dataset.cartId;
            const price = parseFloat(this.dataset.price);

            const cartItem = this.closest(".cart-item");
            const itemTotal = cartItem.querySelector(".cart-item-total");

            const maxStock = parseInt(this.max);

            /*
            | Make sure quantity is valid
            */

            if (isNaN(quantity) || quantity < 1) {
                quantity = 1;
            }

            if (quantity > maxStock) {
                quantity = maxStock;
            }

            this.value = quantity;


            /*
            | Update item price immediately
            */

            const newItemTotal = price * quantity;

            itemTotal.textContent =
                "₱" + newItemTotal.toLocaleString("en-PH", {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });


            /*
            | Send new quantity to PHP
            */

            const formData = new FormData();

            formData.append("cart_id", cartId);
            formData.append("quantity", quantity);


            fetch("update-cart.php", {
                method: "POST",
                body: formData
            })

            .then(response => response.json())

            .then(data => {

                if (!data.success) {

                    alert(data.message);

                    return;
                }


                /*
                | Update subtotal
                */

                const summaryRows =
                    document.querySelectorAll(".summary-row");

                const subtotalElement =
                    summaryRows[0]?.querySelector("strong");

                const totalElement =
                    document.querySelector(".summary-total strong");


                if (subtotalElement) {

                    subtotalElement.textContent =
                        "₱" + data.subtotal;
                }


                /*
                | Update total
                */

                if (totalElement) {

                    totalElement.textContent =
                        "₱" + data.total;
                }

            })

            .catch(error => {

                console.error("Quantity update error:", error);

            });

        });

    });


    /*
    |--------------------------------------------------------------------------
    | REMOVE ITEM
    |--------------------------------------------------------------------------
    */

    const removeButtons =
        document.querySelectorAll(".remove-cart-item");


    removeButtons.forEach(function (button) {

        button.addEventListener("click", function (event) {

            event.preventDefault();

            const removeButton = this;

            const cartId = removeButton.dataset.cartId;

            const cartItem =
                removeButton.closest(".cart-item");


            /*
            | Remove visually immediately
            */

            cartItem.remove();


            /*
            | Send delete request
            */

            fetch(
                "remove-from-cart.php?id=" + encodeURIComponent(cartId),
                {
                    method: "GET"
                }
            )

            .then(response => {

                /*
                | Reload only if the cart is now empty
                */

                const remainingItems =
                    document.querySelectorAll(".cart-item");

                if (remainingItems.length === 0) {
                    window.location.reload();
                }

            })

            .catch(error => {

                console.error("Remove error:", error);

            });

        });

    });

});

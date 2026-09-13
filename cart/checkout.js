function placeOrder() {

    const confirmed = confirm(
        "Are you sure you want to place this order?"
    );

    if (!confirmed) {
        return;
    }

    window.location.href = "place-order.php";

}
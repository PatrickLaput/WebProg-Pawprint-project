document.addEventListener("DOMContentLoaded", function () {

    const paymentType = document.getElementById("payment_type");

    const accountFields = document.getElementById("account-fields");

    const accountName = document.getElementById("account_name");

    const accountNumber = document.getElementById("account_number");

    const accountNameLabel = document.getElementById("account-name-label");

    const accountNumberLabel = document.getElementById("account-number-label");

    const cardNotice = document.getElementById("card-notice");


    function updatePaymentFields() {

        const type = paymentType.value;


        /*
         * Cash on Delivery
         */

        if (type === "Cash on Delivery" || type === "") {

            accountFields.style.display = "none";

            cardNotice.style.display = "none";

            accountName.required = false;

            accountNumber.required = false;

            return;
        }


        /*
         * Show account fields
         */

        accountFields.style.display = "grid";

        accountName.required = true;

        accountNumber.required = true;


        /*
         * GCash
         */

        if (type === "GCash") {

            accountNameLabel.textContent = "Account Name";

            accountNumberLabel.textContent = "GCash Number";

            accountName.placeholder = "Enter GCash account name";

            accountNumber.placeholder = "Enter GCash number";

            accountNumber.inputMode = "numeric";

            accountNumber.maxLength = 11;

            cardNotice.style.display = "none";
        }


        /*
         * Maya
         */

        else if (type === "Maya") {

            accountNameLabel.textContent = "Account Name";

            accountNumberLabel.textContent = "Maya Number";

            accountName.placeholder = "Enter Maya account name";

            accountNumber.placeholder = "Enter Maya number";

            accountNumber.inputMode = "numeric";

            accountNumber.maxLength = 11;

            cardNotice.style.display = "none";
        }


        /*
         * Credit / Debit Card
         */

        else if (type === "Credit / Debit Card") {

            accountNameLabel.textContent = "Cardholder Name";

            accountNumberLabel.textContent = "Card Number";

            accountName.placeholder = "Enter cardholder name";

            accountNumber.placeholder = "Enter 16-digit card number";

            accountNumber.inputMode = "numeric";

            accountNumber.maxLength = 16;

            cardNotice.style.display = "block";
        }
    }


    /*
     * Allow numbers only
     */

    accountNumber.addEventListener("input", function () {

        this.value = this.value.replace(/\D/g, "");

    });


    /*
     * Update fields when payment type changes
     */

    paymentType.addEventListener(
        "change",
        updatePaymentFields
    );


    /*
     * Load fields based on existing payment method
     */

    updatePaymentFields();

});
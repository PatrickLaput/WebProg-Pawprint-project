const profileInput =
    document.getElementById("profile_picture");

const profilePreview =
    document.getElementById("profile-preview");


profileInput.addEventListener("change", function () {

    const file = this.files[0];

    if (file) {

        profilePreview.src =
            URL.createObjectURL(file);

    }

});
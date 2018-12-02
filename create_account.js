
function validateForm() {
    var password = document.forms["create-account"]["password"].value;
    var confirmed_password = document.forms["create-account"]["password2"].value;

    if (password != confirmed_password) {
        alert("Password does not match!");
        return false;
    }
}

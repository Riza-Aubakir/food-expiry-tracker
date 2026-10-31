document.addEventListener("DOMContentLoaded", function () {
    // Батырма басылғанда хабарлама көрсету
    const buttons = document.querySelectorAll(".primary-button");
    buttons.forEach(function (button) {
        button.addEventListener("click", function () {
            console.log("Батырма басылды!");
        });
    });
    // Құпиясөзді көрсету немесе жасыру
    const password = document.querySelector('input[type="password"]');
    if (password) {
        const toggleButton = document.createElement("button");
        toggleButton.type = "button";
        toggleButton.textContent = "Құпиясөзді көрсету";
        toggleButton.style.marginTop = "8px";
        password.insertAdjacentElement("afterend", toggleButton);
        toggleButton.addEventListener("click", function () {
            if (password.type === "password") {
                password.type = "text";
                toggleButton.textContent = "Құпиясөзді жасыру";
            } else {
                password.type = "password";
                toggleButton.textContent = "Құпиясөзді көрсету";
            }
        });
    }
});
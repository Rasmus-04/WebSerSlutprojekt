window.onload = function(){
    const menu_btn = document.querySelector(".hamburger");
    const mobile_menu = document.querySelector(".mobile-nav");
    const test = document.querySelector(".test");

    menu_btn.addEventListener("click", function(){
        menu_btn.classList.toggle("is-active");
        mobile_menu.classList.toggle("is-active");
    });
}

var password = document.getElementById("password")
var confirm_password = document.getElementById("confirm_password");

function validatePassword(){
  if(password.value != confirm_password.value) {
    confirm_password.setCustomValidity("Lösenorden är inte identiska");
  } else {
    confirm_password.setCustomValidity('');
  }
}

password.onchange = validatePassword;
confirm_password.onkeyup = validatePassword;
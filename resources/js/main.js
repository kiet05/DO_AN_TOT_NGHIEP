/* Full Screen */
const fullscreenButton = document.getElementById('sherah-header__full');
const htmlElement = document.documentElement;

fullscreenButton.addEventListener('click', () => {
  if (document.fullscreenElement) {
	document.exitFullscreen();
  } else {
	htmlElement.requestFullscreen();
  }
});


/* Dark Mode */
const button = document.getElementById("sherah-dark-light-button");
const action = document.querySelectorAll("#sherah-sidebarmenu__dark, #sherah-dark-light");

button.addEventListener("click", function() {
    action.forEach((el) => {
        el.classList.toggle("active");
    });
    localStorage.setItem("isDark", action[0].classList.contains("active"));
});

if (localStorage.getItem("isDark") === "true") {
    action.forEach((el) => {
        el.classList.add("active");
    });
}




/* Sherah Sidebar Menu */
const cs_button = document.querySelectorAll(".sherah__sicon");
const cs_action = document.querySelectorAll(".sherah-smenu, .sherah-header, .sherah-adashboard");

cs_button.forEach(button => {
    button.addEventListener("click", function() {
        cs_action.forEach((el) => {
            el.classList.toggle("sherah-close");
        });
        localStorage.setItem("iscicon", cs_action[0].classList.contains("sherah-close"));
    });
});

if (localStorage.getItem("iscicon") === "true") {
    cs_action.forEach((el) => {
        el.classList.add("sherah-close");
    });
}


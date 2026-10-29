// Name: Hamed Zon
// ID: C00259228
// Date: 15/10/2026
// Description: This js handles interactive features on the CareMatch AI login page.

// this lines find the password input and toggleButton
const passwordInput = document.getElementById("password");
const toggleButton  =  document.getElementById("togglePassword");

//this function  hides and shows password when user clickes the button
toggleButton.addEventListener("click", function(){
    const isHidden = passwordInput.type === "password";
    passwordInput.type = isHidden ? "text" : "password";
    toggleButton.textContent = isHidden ? "Hide Password" : "Show Password";
    toggleButton.setAttribute("aria-pressed",String(isHidden));
})
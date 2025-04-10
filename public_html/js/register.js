document.querySelector('#register').addEventListener('click', ()=>{
    let fd = new FormData();
    fd.append("username", document.querySelector("#username").value);
    fd.append("class", document.querySelector("#class").value);
    fd.append("password", document.querySelector("#password").value);
    fd.append("confirmPassword", document.querySelector("#confirmPassword").value);

    logAction("register", fd, "login")
})
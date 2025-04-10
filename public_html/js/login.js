document.querySelector('#login').addEventListener('click', ()=>{
    let fd = new FormData();
    fd.append("username", document.querySelector("#username").value);
    fd.append("password", document.querySelector("#password").value);

    logAction("login", fd, "./")
})
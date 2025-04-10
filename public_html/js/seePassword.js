seePassword = document.querySelector("#seePassword")
if(seePassword != null){
    seePassword.addEventListener("click", ()=> {
        ["password", "confirmPassword"].forEach(field => {
            let pass = document.querySelector(`#${field}`)
            if(pass != null){
                pass.type = pass.type == "password" ? "text" : "password"
            }
        })
    })
}
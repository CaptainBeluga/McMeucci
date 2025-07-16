function cart(e,action){
    let fd = new FormData();
    fd.append("itemID", e.target.getAttribute("item-id"))
    fd.append("action", action)

    fetch("index", {
        method: "POST",
        body: fd

    }).then(resp => resp.text()).then(resp => {
        try{
            alert(JSON.parse(resp)["error"]);
        }
        catch{
            resp = new DOMParser().parseFromString(resp, "text/html")

            if(resp.querySelector("title").textContent == "McMeucci - Login"){
                window.location.href = "login";
            }

            else{
                document.body.innerHTML = resp.body.innerHTML
            
                let cartView = document.querySelector("#cartView")
                
                if(cartView != null){
                    cartView.scrollIntoView()
                }
        
                ["cartManagement"].forEach(script => {
                    let c = document.createElement("script")
                    c.src = `./js/${script}.js`
                    document.body.appendChild(c)
                })
            }  
        }
    })
}


document.querySelectorAll("#addCart").forEach(btn => {

    btn.addEventListener("click", e=>{
        cart(e,"+")
    })
})


document.querySelectorAll("#removeCart").forEach(btn => {

    btn.addEventListener("click", e=>{
       cart(e, "-")
    })
})

document.querySelectorAll("#clearItem").forEach(btn => {

    btn.addEventListener("click", e=>{
       cart(e, "*")
    })
})



try{

    document.querySelector("#clearCartOpen").addEventListener("click", () => {
        new bootstrap.Modal(document.querySelector("#clearCartModal")).show()
    })

    document.querySelector("#clearCart").addEventListener("click", e => {
        cart(e, "/")
    })
    
}catch{} //pass excpetion (0 items in the cart)
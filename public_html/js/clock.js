setInterval(()=>{
    fetch("./util/clock.php", {
        method: "POST"
    }).then(resp => resp.text()).then(resp => {
        try{
            document.querySelector("#clock").textContent = JSON.parse(resp)["time"]
        }
        catch{   
            //Auto Disconnection
            try{
                if(new DOMParser().parseFromString(resp, "text/html").querySelector("title").textContent == "McMeucci - Login"){
                    window.location.href = "logout";
                }
            }catch{} //pass exception (timer >= 15)
        }
    })
},1000)
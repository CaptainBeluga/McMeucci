let statusLabels = document.querySelectorAll(".card-header")
let basicClass = "spinner-grow spinner-grow-sm me-2"

try{
    setInterval(()=>{
        fetch("./util/status.php", {method: "POST"}).then(resp => resp.json())
        .then(resp => {
            Object.keys(resp).forEach(key => {
                data = resp[key].split("-")
                statusLabel = statusLabels[key]

                statusLabel.querySelector("span").textContent = data[0]
                
                statusLabel.querySelectorAll("#statusBlinker").forEach(spinner => {
                    spinner.className = `${basicClass} text-${data[1]}`
                })
            })
        })
    }, 500);
}catch{} //pass exceptions (JWT Expired -> 301 to login.php)
let statusLabels = document.querySelectorAll(".card-header")
let basicClass = "spinner-grow spinner-grow-sm me-2"

try{
    setInterval(()=>{
        fetch("./util/status").then(r => r.json())
        .then(r => {
            Object.keys(r).forEach(key => {
                data = r[key].split("-")
                statusLabel = statusLabels[key]

                statusLabel.querySelector("span").textContent = data[0]
                
                statusLabel.querySelectorAll("#statusBlinker").forEach(spinner => {
                    spinner.className = `${basicClass} text-${data[1]}`
                })
            })
        })
    }, 1000);
}catch{} //pass exceptions (JWT Expired -> 301 to login.php)
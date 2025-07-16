function zeroFormat(number){
    return number.toString().padStart(2, "0");
}

document.addEventListener("DOMContentLoaded", () => {
    let timer = 0;
    const DOMClock = document.querySelector("#clock")
    const extendedClock = document.querySelector("#extendedClock")

    DOMClock.addEventListener("mouseenter", () => {
        extendedClock.classList.remove("d-none")
    })
    DOMClock.addEventListener("mouseleave", () => {
        extendedClock.classList.add("d-none")
    })

    fetch("./util/clock").then(r => r.json()).then(r => {
        timer = r["timer"]

        setInterval(()=>{
            const dateTime = {
                day: Math.floor(timer / (24 * 3600)),
                hour: Math.floor((timer % (24 * 3600)) / 3600),
                minute: Math.floor((timer % 3600) / 60),
                second: timer % 60
            }

            if(dateTime.day == 0 && dateTime.hour == 0 && dateTime.minute < 15){
                DOMClock.classList.add("text-danger")

                if(dateTime.minute == 0 && dateTime.second <= 0){
                    window.location.href="./logout"
                }
            }

            DOMClock.textContent = Object.values(dateTime).map(v => zeroFormat(v)).join(":")
            extendedClock.textContent = Object.keys(dateTime).map(k => `${dateTime[k]} ${k}${dateTime[k] != 1 ? "s" : ""}`).join(", ")

            timer--;

        },1000)
    })
})
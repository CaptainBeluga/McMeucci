saves = document.querySelectorAll("#saveItem")
edits = document.querySelectorAll("#editItem")
deletes = document.querySelectorAll("#deleteItem")


function disableButtons(arr, source){
    arr.forEach(a => {
        if(a != source.target){ a.setAttribute("disabled", ""); }
    })
}

function editFields(e, editable=false){
    let f = Array.from(e.target.parentElement.parentElement.children)
    let inputs = f.slice(0,f.length-3)
    let editInputs = []

    if(editable){
        inputs.forEach(i => {
            if(i.getAttribute("edit-field") != null){ editInputs.push(i) }
        })
    }

    return [editable ? editInputs : inputs, f.slice(f.length-3)]
    
}

function applyResponse(e, fd, method="GET", modalClose=false){
    let t = mandatoryData(e)[0]

    if(method == "POST"){
        fd.append("csrf", document.querySelector("[type=hidden]").value)
    }

    fetch("admin", {
        method: method,
        body: fd

    }).then(r => r.text()).then(r => {

        document.body.innerHTML = r

        let f = document.querySelector(`[table-id=${t}]`).parentElement.parentElement.parentElement.parentElement.parentElement

        f.children[1].classList.add("show")
        f.children[0].children[0].classList.remove("collapsed")


        if(modalClose){
            document.body.style = ""
        }

        let c = document.createElement("script")
        c.src = "./util/adminJS"
        document.body.appendChild(c)
    })
}


function mandatoryData(e){
    return [e.target.parentElement.parentElement.parentElement.getAttribute("table-id"), e.target.parentElement.parentElement.getAttribute("dbID")]
}


function editMode(e){
    e.target.textContent = "CANCEL"
    e.target.className = "btn btn-primary"

    disableButtons(edits, e)
    disableButtons(deletes, e)

    let inputs = editFields(e)[0]

    editFields(e)[1][0].querySelector("button").removeAttribute("disabled") 

    inputs.forEach(i => {
        attr = i.getAttribute("edit-field")

        if(attr != null){
            switch(attr){

                case "checkbox":
                    i.innerHTML = `<input class="form-check-input" type="${attr}" ${i.textContent == "✔️" ? "checked" : ""}>`
                    break


                case "select":
                    i.innerHTML = `<select class="form-select text-center mx-auto">
                                            <option value="PPY" ${i.textContent == "PAYED" ? "selected" : ""}>PAYED</option>
                                            <option value="DLV" ${i.textContent == "DELIVERED" ? "selected" : ""}>DELIVERED</option>
                                    </select>`
                    break

                
                case "number":
                    i.innerHTML = `<input type="number" class="form-control text-center mx-auto mt-2" value=${i.textContent == "❌" ? 0 : i.textContent}>`
                    break

                case "text":
                    i.innerHTML = `<textarea type="text" class="form-control text-center mx-auto mt-2">${i.textContent}</textarea>`
                    break
            }
        }
    })
}



edits.forEach(edit => {
    edit.addEventListener("click", e => {
        editMode(e) 

        e.target.addEventListener("click", src => {
            applyResponse(e)
        })
    })
})




saves.forEach(save => {
    save.addEventListener("click", e => {
        let fd = new FormData()

        fd.append("action", "edit")
        fd.append("table", mandatoryData(e)[0])
        fd.append("id", mandatoryData(e)[1])

        editFields(e,true)[0].forEach(ai => {
            let value;
            
            switch(ai.getAttribute("edit-field")){      
                case "checkbox":
                    value = ai.children[0].checked
                    break

                default:
                    value = ai.children[0].value
            }
            
            fd.append(ai.getAttribute("dbName"), value)
        })

        applyResponse(e, fd, "POST")
        
    })
})




deletes.forEach(d => {
    
    d.addEventListener("click", e => {
        new bootstrap.Modal(document.querySelector("#deleteItemModal")).show()

        document.querySelector("#deleteItem").addEventListener("click", () => {
            let fd = new FormData()
            fd.append("action", "delete")
            fd.append("table", mandatoryData(e)[0])
            fd.append("id", mandatoryData(e)[1])

            applyResponse(e, fd, "POST", true)
        })
    })
})

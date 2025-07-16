function logAction(url, formData, redirectURL){
    grecaptcha.ready(() => {
        grecaptcha.execute("6LcvpF0qAAAAAMHiFnUoBEsB6WeHoMy1Baqrmcll", {action: 'submit'}).then(token => {
            formData.append("captcha", token)
            fetch(url, {
                method: "POST",
                body: formData
                
            }).then(resp => resp.json()).then(resp => {
                if(!resp["success"]){
                    document.querySelector("#errorLabel").textContent = resp["error"]
                }
        
                else{
                    window.location.href = redirectURL;
                }
            })
        });
    });       
}

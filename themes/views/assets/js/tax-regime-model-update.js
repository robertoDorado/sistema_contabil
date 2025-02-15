let taxRegimeModelFormUpdateEndpoint = window.location.pathname
taxRegimeModelFormUpdateEndpoint = taxRegimeModelFormUpdateEndpoint.split("/")
const taxRegimeModelFormUpdateUuid = taxRegimeModelFormUpdateEndpoint.pop()

if (taxRegimeModelFormUpdateEndpoint.join("/") === "/admin/tax-regime/form/update") {
    const taxRegimeForm = document.getElementById("taxRegimeForm")
    taxRegimeForm.addEventListener("submit", function (event) {
        event.preventDefault()
        const inputsElements = Array.from(this.querySelectorAll("input"))
        const emptyInputsElements = inputsElements.filter((elem) => !elem.value)
        
        if (emptyInputsElements.length > 0) {
            emptyInputsElements.forEach(function (input) {
                toastr.error(`campo ${input.dataset.name} está vazio`)
            })
            
            const emptyInputsElementsFormated = emptyInputsElements.map(element => element.dataset.name)
            throw new Error(`campos ${emptyInputsElementsFormated.join(", ")} não podem estar vazios`)
        }

        const btnSubmit = this.querySelector(".btn.btn-primary")
        const btnSubmitText = btnSubmit.innerText
        showSpinner(btnSubmit)
        
        const form = new FormData(this)
        form.append("uuid", taxRegimeModelFormUpdateUuid)
        
        fetch(taxRegimeModelFormUpdateEndpoint.join("/"), {
            method: "POST",
            body: form
        }).then(response => response.json()).then((response) => {
            btnSubmit.removeAttribute("disabled")
            btnSubmit.innerHTML = btnSubmitText
            let message = ""

            if (response.error) {
                message = response.error
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.error(message)
                throw new Error(message)
            }

            if (response.success) {
                message = response.success
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.success(message)
            }
        })
    })
}
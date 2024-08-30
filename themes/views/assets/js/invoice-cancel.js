const invoiceEndpoint = window.location.pathname.replace(/\/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/, "")
const invoiceUuid = Array.isArray(window.location.pathname.match(/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/)) ?
window.location.pathname.match(/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/)[0] : ""

if (invoiceEndpoint == "/admin/invoice/cancel/nfe") {
    const invoiceCancelForm = document.getElementById("invoiceCancelForm")
    const pfxFile = document.querySelector("[name='pfxFile']")
    const verifyExtensionFile = ["pfx"]

    pfxFile.addEventListener("change", function () {
        const extensionName = extensionFileName(this.value)

        if (verifyExtensionFile.indexOf(extensionName) == -1) {
            this.value = ""
            this.nextElementSibling.innerHTML = standardLabelNameExcelFile
            toastr.warning("Extensão do arquivo não permitida")
            throw new Error("Extensão do arquivo não permitida")
        }

        this.nextElementSibling.innerHTML = this.files[0].name
    })

    invoiceCancelForm.addEventListener("submit", function(event) {
        event.preventDefault()
        const btnSubmit = this.querySelector("[type='submit']")
        let fieldsRequired = Array.from(this.querySelectorAll("input,select,textarea"))

        fieldsRequired = fieldsRequired.filter(element => !element.dataset.notrequired)
        fieldsRequired = fieldsRequired.filter(element => !element.value)

        if (fieldsRequired.length > 0) {
            fieldsRequired.forEach(function(element) {
                if (!element.value) {
                    toastr.error(`${element.dataset.name} é um campo obrigatório`)
                }
            })

            let fieldsName = fieldsRequired.map(element => element.dataset.name)
            fieldsName = fieldsName.join(", ")
            throw new Error(`${fieldsName} é um campo obrigatório`)
        }

        showSpinner(btnSubmit)
        const form = new FormData(this)
        form.append("uuid", invoiceUuid)

        fetch(window.location.origin + "/admin/invoice/cancel/nfe", {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function(response) {
            btnSubmit.removeAttribute("disabled")
            btnSubmit.innerHTML = 'Enviar'
            let message = ''
            let toastrMessage = ''

            if (response.error) {
                message = response.error
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastrMessage = response.message ? `${message} (${response.message})` : `${message}`
                toastr.error(toastrMessage)
                throw new Error(toastrMessage)
            }

            if (response.success) {
                message = response.success
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastrMessage = response.message ? `${message} (${response.message})` : `${message}`
                toastr.success(toastrMessage)
            }
        })
    })
}
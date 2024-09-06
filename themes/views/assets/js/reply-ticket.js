const endpointSupportReplyTicket = window.location.pathname.replace(/\/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/, "")
const uuidSupportReplyTicket = Array.isArray(window.location.pathname.match(/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/)) ?
window.location.pathname.match(/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/)[0] : ""

if (endpointSupportReplyTicket == "/admin/support/tickets/reply") {
    const supportTicketsFormReply = document.getElementById("supportTicketsFormReply")
    const attachmentFile = document.getElementById("attachmentFile")

    supportTicketsFormReply.addEventListener("submit", function(event) {
        event.preventDefault()
        const btnSubmit = this.querySelector("#launchBtn")
        const allFields = Array.from(this.querySelectorAll("textarea,input,select"))
        
        let fieldsRequired = allFields.filter(element => !element.dataset.notrequired)
        fieldsRequired = fieldsRequired.filter(element => !element.value)

        if (fieldsRequired.length > 0) {
            fieldsRequired.forEach(function(element) {
                toastr.warning(`O campo ${element.dataset.name} é obrigatório`)
            })

            fieldsRequired = fieldsRequired.map(element => element.dataset.name)
            fieldsRequired = fieldsRequired.join(", ")
            throw new Error(`Os campos ${fieldsRequired} são obrigatórios`)
        }

        showSpinner(btnSubmit)
        const form = new FormData(this)
        form.append("uuid", uuidSupportReplyTicket)

        fetch(window.location.origin + "/admin/support/tickets/reply", {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function(response) {
            btnSubmit.removeAttribute("disabled")
            btnSubmit.innerHTML = "Responder"
            let message = ''
            
            if (response.error) {
                message = response.error
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.error(message)
                throw new Error(message)
            }

            if (response.success) {
                modal.style.display = 'flex'
                setTimeout(() => {
                    window.location.href = response.url
                }, 2000)
            }
        })
    })

    attachmentFile.addEventListener("change", function () {
        const verifyExtension = ["jpg", "png"]
        const extensionName = extensionFileName(this.value)

        if (verifyExtension.indexOf(extensionName) == -1) {
            this.value = ""
            this.nextElementSibling.innerHTML = standardLabelNameExcelFile
            toastr.warning("Extensão do arquivo não permitida")
            throw new Error("Extensão do arquivo não permitida")
        }

        this.nextElementSibling.innerHTML = this.files[0].name
    })
}
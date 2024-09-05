const endpointSupportDetail = window.location.pathname.replace(/\/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/, '')
const uuidSupportDetail = Array.isArray(window.location.pathname.match(/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/)) ?
    window.location.pathname.match(/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/)[0] : ""

if (endpointSupportDetail == "/admin/support/my-tickets/update") {
    const supportTicketsFormUpdate = document.getElementById("supportTicketsFormUpdate")
    const attachmentFile = document.getElementById("attachmentFile")
    $("#userSupportData").select2()

    supportTicketsFormUpdate.addEventListener("submit", function (event) {
        event.preventDefault()
        const allFields = Array.from(this.querySelectorAll("select,input,textarea"))
        let fieldsRequired = allFields.filter(element => !element.dataset.notrequired)
        fieldsRequired = fieldsRequired.filter(element => !element.value)
        const btnSubmit = this.querySelector("#launchBtn")

        if (fieldsRequired.length > 0) {
            fieldsRequired.forEach(function (element) {
                if (!element.value) {
                    toastr.warning(`O campo ${element.dataset.name} não pode estar vazio`)
                }
            })

            fieldsRequired = fieldsRequired.map(element => element.dataset.name)
            fieldsRequired = fieldsRequired.join(", ")
            throw new Error(`Os campos ${fieldsRequired} são obrigatórios`)
        }

        const form = new FormData(this)
        form.append("uuid", uuidSupportDetail)
        showSpinner(btnSubmit)
        fetch(window.location.origin + "/admin/support/my-tickets/update", {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function (response) {
            btnSubmit.removeAttribute("disabled")
            btnSubmit.innerHTML = "Atualizar"
            let message = ''

            if (response.error) {
                message = response.error
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.error(message)
                throw new Error(message)
            }

            if (response.success) {
                message = response.success
                message = message.charAt(0).toUpperCase() + message.slice(1)
                modal.style.display = "flex"
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
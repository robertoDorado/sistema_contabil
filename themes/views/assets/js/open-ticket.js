if (window.location.pathname == "/admin/support/open/ticket") {
    const attachmentFile = document.getElementById("attachmentFile")
    const supportTicketsForm = document.getElementById("supportTicketsForm")
    $("#userSupportData").select2()

    supportTicketsForm.addEventListener("submit", function (event) {
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
        showSpinner(btnSubmit)
        fetch(window.location.origin + "/admin/support/open/ticket", {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function (response) {
            btnSubmit.removeAttribute("disabled")
            btnSubmit.innerHTML = "Enviar"
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
                toastr.success(message)

                const cleanFields = allFields.filter(element => element.name != 'csrfToken')
                cleanFields.forEach(function (element) {
                    element.value = ''
                })

                attachmentFile.value = ''
                attachmentFile.nextElementSibling.innerHTML = 'Nome do arquivo'
                $("#userSupportData").val(null).trigger("change")
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
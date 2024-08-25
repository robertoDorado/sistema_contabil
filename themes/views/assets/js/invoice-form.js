if (window.location.pathname == "/admin/invoice/form") {
    const invoiceForm = document.getElementById("invoiceForm")
    const pfxFile = document.querySelector("[name='pfxFile']")
    const verifyExtensionFile = ["pfx"]
    let fieldsRequired = Array.from(document.querySelectorAll("input,select"))
    $("#municipalityInvoice").select2()

    const companyZipcode = document.querySelector("[name='companyZipcode']")
    companyZipcode.addEventListener("input", function () {
        const searchField = this.value.replace(/\D/g, "").replace(/(\d{5})(\d)/, "$1$2")
        if (searchField.length >= 8) {
            fetch(`https://brasilapi.com.br/api/cep/v1/${searchField}`)
                .then(data => data.json()).then(function (response) {
                    if (response.cep) {
                        document.querySelector("[name='companyAddress']").value = response.street
                        document.querySelector("[name='companyNeighborhood']").value = response.neighborhood
                        document.querySelector("[name='companyCity']").value = response.city
                        document.querySelector("[name='companyState']").value = response.state
                    }
                })
        }
    })

    const mask = companyMaskForm()
    fieldsRequired.forEach(function(element) {
        element.addEventListener("input", function() {
            if (this.dataset.mask) {
                this.value = mask[this.dataset.mask](this.value)
            }
        })
    })

    pfxFile.addEventListener("change", function() {
        const extensionName = extensionFileName(this.value)

        if (verifyExtensionFile.indexOf(extensionName) == -1) {
            this.value = ""
            this.nextElementSibling.innerHTML = standardLabelNameExcelFile
            toastr.warning("Extensão do arquivo não permitida")
            throw new Error("Extensão do arquivo não permitida")
        }

        this.nextElementSibling.innerHTML = this.files[0].name
    })

    invoiceForm.addEventListener("submit", function (event) {
        event.preventDefault()
        fieldsRequired = fieldsRequired.filter((element) => !element.dataset.notrequired)
        fieldsRequired = fieldsRequired.filter((element) => !element.value)

        if (fieldsRequired.length > 0) {
            fieldsRequired.forEach(function(element) {
                if (element.dataset.name) {
                    toastr.error(`O campo ${element.dataset.name} não pode estar vazio`)
                }
            })

            let fieldsName = fieldsRequired.map((element) => element.dataset.name)
            fieldsName = fieldsName.join(", ")
            throw new Error(`campos ${fieldsName} obrigatórios`)
        }

        const btnSubmit = this.querySelector("[type='submit']")
        // showSpinner(btnSubmit)

        const form = new FormData(this)
        fetch(window.location.origin + "/admin/invoice/form", {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function(response) {
            btnSubmit.removeAttribute("disabled")
            btnSubmit.innerHTML = "Emitir Nota Fiscal"
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
if (window.location.pathname == "/admin/invoice/form") {
    const invoiceForm = document.getElementById("invoiceForm")
    const pfxFile = document.querySelector("[name='pfxFile']")
    const verifyExtensionFile = ["pfx"]
    const recipientDocumentType = document.getElementById("recipientDocumentType")
    const recipientDocument = document.getElementById("recipientDocument")
    const companyContactType = document.getElementById("companyContactType")
    const companyPhone = document.getElementById("companyPhone")
    const recipientContactType = document.getElementById("recipientContactType")
    const recipientPhone = document.getElementById("recipientPhone")
    let fieldsRequired = Array.from(invoiceForm.querySelectorAll("input,select"))

    $(`#productValueUnit,
        #productTotalValue,
        #taxUnitValue,
        #productShippingValue,
        #productInsuranceValue,
        #productDiscountAmount,
        #productValueOtherExpenses,
        #totalTaxValue,
        #paymentTotalValue,
        #changeMoney
    `).maskMoney(
        {
            allowNegative: false,
            thousands: '.',
            decimal: ',',
            affixesStay: false
        }
    )

    $('#form-wizard').bootstrapWizard({
        'tabClass': 'nav nav-pills',
        'nextSelector': '.wizard .next',
        'previousSelector': '.wizard .previous'
    })

    $("#municipalityInvoice,#recipientMunicipality,#codeMethodPayment").select2()
    document.querySelectorAll(".select2.select2-container.select2-container--default").forEach(function (element) {
        element.style.width = "100%"
    })

    const dataTransfer = {
        documentType: null,
        phoneType: null
    }

    recipientDocumentType.addEventListener("change", function () {
        const validateDocumentType = {
            '1': 'cpf',
            '2': 'cnpj'
        }
        dataTransfer.documentType = validateDocumentType[this.value]
        recipientDocument.dataset.mask = validateDocumentType[this.value]
    })

    companyContactType.addEventListener("change", function () {
        const validateContactType = {
            '1': 'phone',
            '2': 'cellPhone'
        }
        dataTransfer.phoneType = validateContactType[this.value]
        companyPhone.dataset.mask = validateContactType[this.value]
    })

    recipientContactType.addEventListener("change", function () {
        const validateContactType = {
            '1': 'phone',
            '2': 'cellPhone'
        }
        dataTransfer.phoneType = validateContactType[this.value]
        recipientPhone.dataset.mask = validateContactType[this.value]
    })

    const mask = companyMaskForm()
    recipientDocument.addEventListener("input", function () {
        if (typeof mask[dataTransfer.documentType] == "function") {
            this.value = mask[dataTransfer.documentType](this.value)
        }
    })

    companyPhone.addEventListener("input", function () {
        if (typeof mask[dataTransfer.phoneType] == "function") {
            this.value = mask[dataTransfer.phoneType](this.value)
        }
    })

    recipientPhone.addEventListener("input", function () {
        if (typeof mask[dataTransfer.phoneType] == "function") {
            this.value = mask[dataTransfer.phoneType](this.value)
        }
    })

    const zipcodeData = document.querySelectorAll("#companyZipcode,#recipientZipcode")
    zipcodeData.forEach(function (element) {
        element.addEventListener("input", function () {
            const searchField = this.value.replace(/\D/g, "").replace(/(\d{5})(\d)/, "$1$2")
            if (searchField.length >= 8) {
                fetch(`https://brasilapi.com.br/api/cep/v1/${searchField}`)
                    .then(data => data.json()).then(function (response) {
                        const verifyAddressType = {
                            "issuer": function (response) {
                                document.querySelector("[name='companyAddress']").value = response.street
                                document.querySelector("[name='companyNeighborhood']").value = response.neighborhood
                                document.querySelector("[name='companyState']").value = response.state
                            },
                            "recipient": function (response) {
                                document.querySelector("[name='recipientAddress']").value = response.street
                                document.querySelector("[name='recipientNeighborhood']").value = response.neighborhood
                                document.querySelector("[name='recipientState']").value = response.state
                            },
                        }
                        if (response.cep) {
                            if (typeof verifyAddressType[element.dataset.addresstype] == "function") {
                                verifyAddressType[element.dataset.addresstype](response)
                            }
                        }
                    })
            }
        })
    })

    const configViewFieldsRequired = fieldsRequired.filter((element) => !element.dataset.notrequired)
    const labelFieldsName = configViewFieldsRequired.map(function(element) {
        if (element.previousElementSibling) {
            return element.previousElementSibling.innerHTML
        }else {
            return element.parentElement.parentElement.previousElementSibling.innerHTML
        }
    })

    configViewFieldsRequired.forEach(function(element, index) {
        if (element.previousElementSibling) {
            element.previousElementSibling.innerHTML = `* ${labelFieldsName[index]}`
        }else {
            element.parentElement.parentElement.previousElementSibling.innerHTML = `* ${labelFieldsName[index]}`
        }
    })

    fieldsRequired.forEach(function (element) {
        element.addEventListener("input", function () {
            if (this.dataset.mask) {
                this.value = mask[this.dataset.mask](this.value)
            }
        })
    })

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

    invoiceForm.addEventListener("submit", function (event) {
        event.preventDefault()
        let fieldsRequired = Array.from(this.querySelectorAll("input,select"))
        fieldsRequired = fieldsRequired.filter((element) => !element.dataset.notrequired)
        fieldsRequired = fieldsRequired.filter((element) => !element.value)

        if (fieldsRequired.length > 0) {
            fieldsRequired.forEach(function (element) {
                if (element.dataset.name) {
                    toastr.error(`O campo ${element.dataset.name} não pode estar vazio`)
                }
            })

            let fieldsName = fieldsRequired.map((element) => element.dataset.name)
            fieldsName = fieldsName.join(", ")
            throw new Error(`campos obrigatórios ${fieldsName}, Total: ${fieldsRequired.length}`)
        }

        const btnSubmit = this.querySelector("[type='submit']")
        // showSpinner(btnSubmit)

        const form = new FormData(this)
        fetch(window.location.origin + "/admin/invoice/form", {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function (response) {
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
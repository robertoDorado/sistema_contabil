if (window.location.pathname == "/admin/bank-reconciliation/cash-flow/automatic") {
    const importOfxFileForm = document.getElementById("importOfxFile")
    const ofxFileInput = document.querySelector("[name='ofxFile']")
    const standardLabelNameExcelFile = ofxFileInput.nextElementSibling.innerHTML
    const totalElement = document.querySelector("tfoot tr").lastElementChild

    automaticReconciliationReport.on('search.dt', function() {
        const dataFilter = automaticReconciliationReport.rows({ search: 'applied' }).data();
        let balance = 0

        dataFilter.each(function (row) {
            let entryValue = parseFloat(row[2].replace("R$", "")
                .replace(".", "").replace(",", ".").trim())

            balance += entryValue
        })

        totalElement.innerHTML = balance
        .toLocaleString("pt-br", { "currency": "BRL", "style": "currency" })
    })

    importOfxFileForm.addEventListener("submit", function (event) {
        event.preventDefault()
        const extensionName = extensionFileName(this.ofxFile.value)
        const btnSubmit = this.querySelector('[type="submit"]')
        const importIcon = document.querySelector(".fa.fa-cloud-upload")
        const ofxFile = this.ofxFile
        const ofxLabel = this.ofxFile.nextElementSibling

        if (extensionName != "ofx") {
            ofxFile.value = ""
            ofxLabel.innerHTML = standardLabelNameExcelFile
            toastr.warning("Extensão do arquivo não permitida")
            throw new Error("Extensão do arquivo não permitida")
        }

        showSpinner(btnSubmit)
        const spinner = document.querySelector(".fas.fa-spinner.fa-spin")
        const form = new FormData(this)

        fetch(window.location.href, {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function (response) {
            spinner.remove()
            btnSubmit.append(importIcon, " Importar ")
            btnSubmit.removeAttribute("disabled")
            let message = ""

            if (response.error) {
                ofxFile.value = ""
                ofxLabel.innerHTML = standardLabelNameExcelFile
                message = response.error.charAt(0).toUpperCase() + response.error.slice(1)
                toastr.error(message)
                throw new Error(message)
            }

            if (response.success) {
                let message = response.success
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.success(message)
                throw { message: message, name: 'StopExecution' }
            }

            if (response.data) {
                for (let i = 0; i < response.data.length; i++) {
                    automaticReconciliationReport.row.add([
                        response.data[i].date,
                        response.data[i].memo,
                        response.data[i].amount_formated
                    ]).draw(false)
                }
                totalElement.innerHTML = response.total
            }
        })
    })

    ofxFileInput.addEventListener("change", function () {
        const extensionName = extensionFileName(this.value)

        if (extensionName != "ofx") {
            this.value = ""
            this.nextElementSibling.innerHTML = standardLabelNameExcelFile
            toastr.warning("Extensão do arquivo não permitida")
            throw new Error("Extensão do arquivo não permitida")
        }

        this.nextElementSibling.innerHTML = this.files[0].name
    })
}
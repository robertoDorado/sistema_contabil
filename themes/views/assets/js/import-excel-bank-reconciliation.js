const endpointsBankReconciliationImportExcel = [
    "/admin/bank-reconciliation/cash-flow/manual",
    "/admin/bank-reconciliation/cash-flow/automatic"
]
if (endpointsBankReconciliationImportExcel.indexOf(window.location.pathname) != -1) {
    const importExcelForm = document.getElementById("importExcelForm")
    const inputExcelFile = document.querySelector('[name="excelFile"]')
    const standardLabelNameExcelFile = inputExcelFile.nextElementSibling.innerHTML
    const verifyExtensionFile = ["xls", "xlsx", "csv"]
    const totalElement = document.querySelector(".reconciliation-report-cash-flow")
        .querySelector("tfoot tr").lastElementChild

    if (window.location.pathname == endpointsBankReconciliationImportExcel[0]) {
        manualReconciliationReportCashFlow.on('search.dt', function () {
            const dataFilter = manualReconciliationReportCashFlow.rows({ search: 'applied' }).data();
            let balance = 0
    
            dataFilter.each(function (row) {
                let entryValue = parseFloat(row[2].replace('R$', '').replace(/\./g, '').replace(',', '.').trim())
                balance += entryValue
            })
    
            totalElement.innerHTML = balance
                .toLocaleString("pt-br", { "currency": "BRL", "style": "currency" })
        })
    }else {
        automaticReconciliationReportCashFlow.on('search.dt', function () {
            const dataFilter = automaticReconciliationReportCashFlow.rows({ search: 'applied' }).data();
            let balance = 0
    
            dataFilter.each(function (row) {
                let entryValue = parseFloat(row[2].replace('R$', '').replace(/\./g, '').replace(',', '.').trim())
                balance += entryValue
            })
    
            totalElement.innerHTML = balance
                .toLocaleString("pt-br", { "currency": "BRL", "style": "currency" })
        })
    }

    inputExcelFile.addEventListener("change", function () {
        const extensionName = extensionFileName(this.value)

        if (verifyExtensionFile.indexOf(extensionName) == -1) {
            this.value = ""
            this.nextElementSibling.innerHTML = standardLabelNameExcelFile
            toastr.warning("Extensão do arquivo não permitida")
            throw new Error("Extensão do arquivo não permitida")
        }

        this.nextElementSibling.innerHTML = this.files[0].name
    })

    importExcelForm.addEventListener("submit", function (event) {
        event.preventDefault()
        const extensionName = extensionFileName(this.excelFile.value)
        const btnSubmit = this.querySelector('[type="submit"]')
        const importIcon = document.querySelector(".fa.fa-cloud-upload")
        const excelFile = this.excelFile
        const excelLabel = this.excelFile.nextElementSibling

        if (verifyExtensionFile.indexOf(extensionName) == -1) {
            excelFile.value = ""
            excelLabel.innerHTML = standardLabelNameExcelFile
            toastr.warning("Extensão do arquivo não permitida")
            throw new Error("Extensão do arquivo não permitida")
        }

        showSpinner(btnSubmit)
        const spinner = document.querySelector(".fas.fa-spinner.fa-spin")
        const form = new FormData(this)

        fetch(window.location.origin + "/admin/bank-reconciliation/cash-flow/import-excel-file", {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function (response) {
            spinner.remove()
            btnSubmit.append(importIcon, " Importar ")
            btnSubmit.removeAttribute("disabled")
            let message = ""

            if (response.error) {
                excelFile.value = ""
                excelLabel.innerHTML = standardLabelNameExcelFile
                message = response.error.charAt(0).toUpperCase() + response.error.slice(1)
                toastr.error(message)
                throw new Error(message)
            }

            if (response.success) {
                message = response.success
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.success(message)
            }

            if (response.data) {
                if (window.location.pathname == endpointsBankReconciliationImportExcel[0]) {
                    for (let i = 0; i < response.data["d"].length; i++) {
                        manualReconciliationReportCashFlow.row.add([
                            response.data["d"][i],
                            response.data["h"][i],
                            response.data["l"][i]
                        ]).draw(false)
                    }
                }else {
                    for (let i = 0; i < response.data.length; i++) {
                        automaticReconciliationReportCashFlow.row.add([
                            response.data[i].created_at,
                            response.data[i].history,
                            response.data[i].entry
                        ]).draw(false)
                    }
                }
            }

            if (response.total) {
                totalElement.innerHTML = response.total
            }
        })
    })
}
if (window.location.pathname == "/admin/balance-sheet/chart-of-account") {
    const importExcelForm = document.getElementById("importExcelForm")
    const inputExcelFile = document.querySelector('[name="excelFile"]')
    const standardLabelNameExcelFile = inputExcelFile.nextElementSibling.innerHTML
    const verifyExtensionFile = ["xls", "xlsx"]

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

        fetch(window.location.origin + "/admin/balance-sheet/chart-of-account/import-file", {
            method: 'POST',
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

            if (response.success && response.data) {
                message = response.success.charAt(0).toUpperCase() + response.success.slice(1)
                toastr.success(message)
                response.data.forEach(function(item) {
                    chartOfAccountTable.row.add([
                        item.uuid,
                        item.account_number,
                        item.account_name,
                        item.edit_btn,
                        item.delete_btn
                    ]).draw(false)
                })
            }
        })
    })
}
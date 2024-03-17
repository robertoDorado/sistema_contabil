if (window.location.pathname == '/admin/cash-flow/report') {
    const importExcelForm = document.getElementById("importExcelForm")
    const inputExcelFile = document.querySelector('[name="excelFile"]')
    const standardLabelNameExcelFile = inputExcelFile.nextElementSibling.innerHTML
    const verifyExtensionFile = ["xls", "xlsx"]

    inputExcelFile.addEventListener("change", function() {
        const extensionName = extensionFileName(this.value)
        if (verifyExtensionFile.indexOf(extensionName) == -1) {
            this.value = ""
            this.nextElementSibling.innerHTML = standardLabelNameExcelFile
            toastr.warning("Extensão do arquivo não permitida")
            throw new Error("Extensão do arquivo não permitida")
        }

        this.nextElementSibling.innerHTML = this.files[0].name
    })

    importExcelForm.addEventListener("submit", function(event) {
        event.preventDefault()
        const extensionName = extensionFileName(this.excelFile.value)

        if (verifyExtensionFile.indexOf(extensionName) == -1) {
            this.excelFile.value = ""
            this.excelFile.nextElementSibling.innerHTML = standardLabelNameExcelFile
            toastr.warning("Extensão do arquivo não permitida")
            throw new Error("Extensão do arquivo não permitida")
        }

        const form = new FormData(this)
        fetch(window.location.origin + "/admin/cash-flow/import-excel", {
            method: 'POST',
            body: form
        }).then(response => response.json()).then(function(response) {
            if (response.error) {
                let message = response.error.charAt(0).toUpperCase() + response.error.slice(1)
                toastr.error(message)
                throw new Error(response.error)
            }
        })
    })
}
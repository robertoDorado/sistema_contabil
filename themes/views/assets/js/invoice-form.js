if (window.location.pathname == "/admin/invoice/form") {
    const invoiceForm = document.getElementById("invoiceForm")
    const pfxFile = document.querySelector("[name='pfxFile']")
    const verifyExtensionFile = ["pfx"]

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
        const btnSubmit = this.querySelector("[type='submit']")
        showSpinner(btnSubmit)

        const form = new FormData(this)
        fetch(window.location.origin + "/admin/invoice/form", {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function(response) {
            btnSubmit.removeAttribute("disabled")
            btnSubmit.innerHTML = "Emitir Nota Fiscal"
            console.log(response)
        })
    })
}
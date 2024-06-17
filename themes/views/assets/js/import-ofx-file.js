if (window.location.pathname == "/admin/bank-reconciliation/cash-flow/automatic") {
    const importOfxFileForm = document.getElementById("importOfxFile")
    const ofxFileInput = document.querySelector("[name='ofxFile']")
    const standardLabelNameExcelFile = ofxFileInput.nextElementSibling.innerHTML

    importOfxFileForm.addEventListener("submit", function(event) {
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
        }).then(response => response.json()).then(function(response) {
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

            console.log(response)
        })
    })

    ofxFileInput.addEventListener("change", function() {
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
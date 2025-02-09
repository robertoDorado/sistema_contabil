if (window.location.pathname === "/admin/tax-regime/form") {
    const taxRegimeBtn = document.querySelector("[tax-regime-btn]")
    const taxRegimeForm = document.querySelector("#taxRegimeForm")

    taxRegimeForm.addEventListener("submit", function (event) {
        event.preventDefault()
        const sendFormBtn = this.querySelector("#launchBtn")
        const btnContent = sendFormBtn.innerHTML
        showSpinner(sendFormBtn)

        if (!this.taxRegimeValue.value) {
            toastr.error("Valor do regime tributário é obrigatório")
            throw new Error("Valor do regime tributário é obrigatório")
        }

        if (!this.csrfToken.value) {
            toastr.error("Valor do token é obrigatório")
            throw new Error("Valor do token é obrigatório")
        }

        const form = new FormData(this)
        fetch("/admin/tax-regime/create", {
            method: "POST",
            body: form
        }).then(response => response.json()).then((response) => {
            sendFormBtn.removeAttribute("disabled")
            sendFormBtn.innerHTML = btnContent
            const responseImport = JSON.parse(response.data ?? "{}")
            let message = ""

            if (response.error) {
                message = response.error
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.error(message)
            }

            if (response.success) {
                message = response.success
                message = message.charAt(0).toUpperCase() + message.slice(1)
                this.taxRegimeValue.value = ""
                taxRegimeReport.row.add([
                    responseImport["uuid"],
                    responseImport["tax_regime_value"],
                    responseImport["edit"],
                    responseImport["delete"]
                ]).draw(false)
                toastr.success(message)
            }
        })
    })

    taxRegimeBtn.addEventListener("click", function () {
        const btnTextContent = this.innerHTML
        showSpinner(this)

        const form = new FormData()
        form.append("fill", true)

        fetch(window.location.pathname, {
            method: "POST",
            body: form
        }).then(response => response.json()).then((response) => {
            this.removeAttribute("disabled")
            this.innerHTML = btnTextContent
            const responseImport = JSON.parse(response.data ?? "{}")
            let message = ""

            const importResponseData = () => {
                for (i = 0; i < responseImport["uuid"].length; i++) {
                    taxRegimeReport.row.add([
                        responseImport["uuid"][i],
                        responseImport["tax_regime_value"][i],
                        responseImport["edit"][i],
                        responseImport["delete"][i]
                    ]).draw(false)
                }
            }

            if (response.warning) {
                message = response.warning
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.warning(message)
                importResponseData()
            }

            if (response.error) {
                message = response.error
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.error(message)
            }

            if (response.success) {
                message = response.success
                message = message.charAt(0).toUpperCase() + message.slice(1)
                importResponseData()
                toastr.success(message)
            }
        })
    })
}
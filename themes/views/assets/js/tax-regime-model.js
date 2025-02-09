if (window.location.pathname === "/admin/tax-regime/form") {
    const taxRegimeBtn = document.querySelector("[tax-regime-btn]")

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
            const responseImport = JSON.parse(response.data)
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
                throw new Error(message)
            }

            if (response.error) {
                message = response.error
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.error(message)
                throw new Error(message)
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
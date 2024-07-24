if (window.location.pathname == "/admin/balance-sheet/chart-of-account-group/form") {
    const mask = {
        accountNumber: function (value) {
            return value.replace(/[^\d\.]+/g, '').replace(/\.(\.)/, '$1')
        }
    }

    const accountNumber = document.querySelector("[name='accountNumber']")
    accountNumber.addEventListener("input", function () {
        this.value = mask[this.dataset.mask](this.value)
    })

    const chartOfAccountGroupForm = document.getElementById("chartOfAccountGroupForm")
    chartOfAccountGroupForm.addEventListener("submit", function(event) {
        event.preventDefault()
        const  btnSubmit = this.querySelector("[type='submit']")

        if (!this.accountNumber.value) {
            toastr.warning("Número da conta é obrigatório")
            throw new Error("Número da conta é obrigatório")
        }

        if (!this.accountName.value) {
            toastr.warning("Nome da conta é obrigatório")
            throw new Error("Nome da conta é obrigatório")
        }

        if (!this.csrfToken.value) {
            toastr.warning("Token csrf é obrigatório")
            throw new Error("Token csrf é obrigatório")
        }

        if (Array.isArray(this.accountNumber.value.match(/\d+\.$/))) {
            toastr.error("Número de conta inválido")
            throw new Error("Número de conta inválido")
        }

        const resetInputValues = [this.accountNumber, this.accountName]
        const form = new FormData(this)
        showSpinner(btnSubmit)
        fetch(window.location.origin + "/admin/balance-sheet/chart-of-account-group/form", {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function(response) {
            btnSubmit.removeAttribute("disabled")
            btnSubmit.innerHTML = "Enviar"
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
                resetInputValues.forEach(function(element) {
                    element.value = ""
                })
            }
        })
    })
}
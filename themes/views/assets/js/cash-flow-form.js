if (window.location.pathname == '/admin/cash-flow/form') {
    const cashFlowForm = document.getElementById("cashFlowForm")
    $("#launchValue").maskMoney(
        {
            allowNegative: false, 
            thousands:'.', 
            decimal:',', 
            affixesStay: false
        }
    )

    const launchBtn = document.getElementById("launchBtn")
    cashFlowForm.addEventListener("submit", function(event) {
        event.preventDefault()

        if (!this.launchValue.value) {
            toastr.warning("Campo valor de entrada inválido")
            throw new Error('Campo valor de entrada é obrigatório')
        }

        if (!this.csrfToken.value) {
            toastr.warning("Campo csrf-token inválido")
            throw new Error("Campo csrf-token inválido")
        }

        if (!this.releaseHistory.value) {
            toastr.warning("Campo histórico inválido")
            throw new Error("Campo histórico inválido")
        }

        if (!this.entryType.value) {
            toastr.warning("Campo tipo de entrada inválido")
            throw new Error("Campo tipo de entrada inválido")
        }

        const cashFlowFormFields = [
            this.launchValue,
            this.releaseHistory,
            this.entryType
        ]
        showSpinner(launchBtn)
        const form = new FormData(this)
        fetch(window.location.href, 
        {
            method: "POST",
            body: form
        }
        ).then((response) => response.json())
        .then(function(response) {
            let message = ''
            launchBtn.innerHTML = 'Enviar'

            if (response.user_not_exists) {
                message = response.user_not_exists
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.error(message)
                throw new Error(message)
            }

            if (response.invalid_entry_type) {
                message = response.invalid_entry_type
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.error(message)
                throw new Error(message)
            }

            if (response.invalid_persist_data) {
                message = response.invalid_persist_data
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.error(message)
                throw new Error(message)
            }
            
            cashFlowFormFields.forEach(function(elem) {
                elem.value = ''
            })
            
            message = response.success
            message = message.charAt(0).toUpperCase() + message.slice(1)
            toastr.success(message)
        })
    })
}
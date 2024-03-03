let parameter = window.location.pathname.split("/")
parameter = parameter.pop()

if (window.location.pathname == `/admin/cash-flow/update/form/${parameter}`) {
    $("#launchValue").maskMoney(
        {
            allowNegative: false, 
            thousands:'.', 
            decimal:',', 
            affixesStay: false
        }
    )

    const cashFlowForm = document.getElementById("cashFlowForm")
    cashFlowForm.addEventListener("submit", function(event) {
        event.preventDefault()
        const launchBtn = document.getElementById("launchBtn")

        if (!this.launchValue.value) {
            toastr.warning("Campo valor de lançamento não pode estar vazio")
            throw new Error("Campo valor de lançamento não pode estar vazio")
        }

        if (!this.releaseHistory.value) {
            toastr.warning("Campo histórico não pode estar vazio")
            throw new Error("Campo histórico não pode estar vazio")
        }

        if (!this.entryType.value) {
            toastr.warning("Tipo de entrada inválida")
            throw new Error("Tipo de entrada inválida")
        }

        showSpinner(launchBtn)
        const form = new FormData(this)
        fetch(window.location.href, {
            method: "POST",
            body: form
        }).then((response) => response.json())
        .then(function(response) {
            let message = ''

            if (response.user_not_exists) {
                message = response.user_not_exists
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.error(message)
                btnSubmit.innerHTML = 'Atualizar'
                throw new Error(message)
            }

            if (response.data_is_empty) {
                message = response.data_is_empty
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.error(message)
                btnSubmit.innerHTML = 'Atualizar'
                throw new Error(message)
            }

            if (response.cash_flow_data_not_found) {
                message = response.cash_flow_data_not_found
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.error(message)
                btnSubmit.innerHTML = 'Atualizar'
                throw new Error(message)
            }

            if (response.success) {
                window.location.href = response.url
            }
        })
    })
}
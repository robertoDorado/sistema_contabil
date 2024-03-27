let cashFlowParameter = window.location.pathname.split("/")
cashFlowParameter = cashFlowParameter.pop()

if (window.location.pathname == `/admin/cash-flow/update/form/${cashFlowParameter}`) {
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
        const updateBtn = document.getElementById("updateBtn")

        if (!this.launchValue.value) {
            toastr.warning("Campo valor de lançamento não pode estar vazio")
            throw new Error("Campo valor de lançamento não pode estar vazio")
        }

        if (!this.releaseHistory.value) {
            toastr.warning("Campo histórico não pode estar vazio")
            throw new Error("Campo histórico não pode estar vazio")
        }

        if (!this.createdAt.value) {
            toastr.warning("Campo data não pode estar vazio")
            throw new Error("Campo data não pode estar vazio")
        }

        if (!this.entryType.value) {
            toastr.warning("Tipo de entrada inválida")
            throw new Error("Tipo de entrada inválida")
        }

        showSpinner(updateBtn)
        const form = new FormData(this)
        fetch(window.location.href, {
            method: "POST",
            body: form
        }).then((response) => response.json())
        .then(function(response) {
            let message = ''

            if (response.empty_cash_flow) {
                updateBtn.innerHTML = 'Atualizar'
                updateBtn.removeAttribute("disabled")
                message = response.empty_cash_flow
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.error(message)
                throw new Error(message)
            }

            if (response.user_not_exists) {
                updateBtn.innerHTML = 'Atualizar'
                updateBtn.removeAttribute("disabled")
                message = response.user_not_exists
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.error(message)
                throw new Error(message)
            }

            if (response.data_is_empty) {
                updateBtn.innerHTML = 'Atualizar'
                updateBtn.removeAttribute("disabled")
                message = response.data_is_empty
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.error(message)
                throw new Error(message)
            }

            if (response.cash_flow_data_not_found) {
                updateBtn.innerHTML = 'Atualizar'
                updateBtn.removeAttribute("disabled")
                message = response.cash_flow_data_not_found
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.error(message)
                throw new Error(message)
            }

            if (response.invalid_date) {
                updateBtn.innerHTML = 'Atualizar'
                updateBtn.removeAttribute("disabled")
                message = response.invalid_date
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.error(message)
                throw new Error(message)
            }

            if (response.error) {
                updateBtn.innerHTML = 'Atualizar'
                updateBtn.removeAttribute("disabled")
                message = response.error
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.error(message)
                throw new Error(message)
            }

            if (response.success) {
                window.location.href = response.url
            }
        })
    })
}
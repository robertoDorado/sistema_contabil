if (window.location.pathname == "/admin/cash-variation-setting/operating-cash-flow/form") {
    const accountGroup = $("#accountGroup").select2()
    document.getElementById("operatingCashFlowForm").addEventListener("submit", function(event) {
        event.preventDefault()
        const btnSubmit = this.querySelector("[type='submit']")

        let message = ""
        if (!this.accountGroup.value) {
            message = "O campo grupo de contas está vazio ou inválido"
            toastr.warning(message)
            throw new Error(message)
        }

        if (!this.csrfToken.value) {
            message = "O campo token csrf está vazio ou inválido"
            toastr.warning(message)
            throw new Error(message)
        }

        const form = new FormData(this)
        showSpinner(btnSubmit)

        fetch(window.location.href, {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function(response) {
            btnSubmit.removeAttribute("disabled")
            btnSubmit.innerHTML = "Enviar"
            accountGroup.val(null).trigger("change")
            
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
            }
        })
    })
}
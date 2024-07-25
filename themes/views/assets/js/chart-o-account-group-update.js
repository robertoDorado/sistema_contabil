const chartOfAccountGroupEndpoint = window.location.pathname.replace(/\/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/, '')

if (chartOfAccountGroupEndpoint == "/admin/balance-sheet/chart-of-account-group/update") {
    const chartOfAccountGroupUuid = Array.isArray(window.location.pathname.match(/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/))
    ? window.location.pathname.match(/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/)[0] : ""

    const chartOfAccountGroupFormUpdate = document.getElementById("chartOfAccountGroupFormUpdate")
    chartOfAccountGroupFormUpdate.addEventListener("submit", function(event) {
        event.preventDefault()
        const btnSubmit = this.querySelector("[type='submit']")

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

        if (Array.isArray(this.accountNumber.value.match(/^\.$/))) {
            toastr.error("Número da conta inválido")
            throw new Error("Número de conta inválido")
        }

        const form = new FormData(this)
        form.append("uuid", chartOfAccountGroupUuid)
        showSpinner(btnSubmit)
        fetch(window.location.origin + "/admin/balance-sheet/chart-of-account-group/update", {
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
                modal.style.display = "flex"
                window.location.href = response.url
            }
        })
    })
}
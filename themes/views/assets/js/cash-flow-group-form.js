if (window.location.pathname == "/admin/cash-flow-group/form") {
    const cashFlowGroupForm = document.getElementById("cashFlowGroupForm")
    cashFlowGroupForm.addEventListener("submit", function(event) {
        event.preventDefault()
        const btnSubmit = this.querySelector("[type='submit']")
        let accountGroup = this.accountGroup
        
        if (!this.csrfToken.value) {
            toastr.warning("Campo token não pode estar vazio")
            throw new Error("Campo token não pode estar vazio")
        }

        if (!accountGroup.value) {
            toastr.warning("Campo nome do grupo não pode estar vazio")
            throw new Error("Campo nome do grupo não pode estar vazio")
        }

        showSpinner(btnSubmit)
        const form = new FormData(this)
        fetch(window.location.origin + "/admin/cash-flow-group/form", {
            method: "POST",
            body: form
        }).then((response) => response.json()).then(function(response) {
            let message = ""
            btnSubmit.innerHTML = "Enviar"

            if (response.error) {
                message = response.error.charAt(0).toUpperCase() + response.error.slice(1)
                toastr.error(message)
                throw new Error(message)
            }

            accountGroup.value = ""
            message = response.success.charAt(0).toUpperCase() + response.success.slice(1)
            toastr.success(message)
        })
    })
}
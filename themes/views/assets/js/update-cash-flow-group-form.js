let cashFlowGroupParameter = window.location.pathname.split("/")
cashFlowGroupParameter = cashFlowGroupParameter.pop()

if (window.location.pathname == `/admin/cash-flow-group/update/form/${cashFlowGroupParameter}`) {
    const cashFlowGroupForm = document.getElementById("cashFlowGroupForm")
    cashFlowGroupForm.addEventListener("submit", function(event) {
        event.preventDefault()
        const btnSubmit = this.querySelector("[type='submit']")

        if (!this.accountGroup.value) {
            toastr.warning("Campo nome grupo de contas é obrigatório")
            throw new Error("Campo nome grupo de contas é obrigatório")
        }

        if (!this.csrfToken.value) {
            toastr.warning("Campo token é obrigatório")
            throw new Error("Campo token é obrigatório")
        }

        const form = new FormData(this)
        showSpinner(btnSubmit)
        fetch(window.location.href,
        {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function(response) {
            let message = ""

            if (response.error) {
                btnSubmit.innerHTML = "Atualizar"
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
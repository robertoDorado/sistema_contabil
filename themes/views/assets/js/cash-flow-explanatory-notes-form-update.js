let endpointCashFlowExplanatoryNoteUpdate = window.location.pathname.replace(/(\/\w{8}-\w{4}-\w{4}-\w{4}-\w{12})/, "")
let cashFlowExplanatoryNoteUuid = window.location.pathname.match(/(\w{8}-\w{4}-\w{4}-\w{4}-\w{12})/)

if (Array.isArray(cashFlowExplanatoryNoteUuid)) {
    cashFlowExplanatoryNoteUuid = cashFlowExplanatoryNoteUuid.length > 0 ? cashFlowExplanatoryNoteUuid[0] : ""
}

if (endpointCashFlowExplanatoryNoteUpdate == "/admin/cash-flow-explanatory-notes/form/update") {
    document.getElementById("cashFlowExplanatoryNotesForm").addEventListener("submit", function(event) {
        event.preventDefault()
        let message = ""
        const btnSubmit = this.querySelector("[type='submit']")

        if (!this.explanatoryNoteText.value) {
            message = "O campo nota é obrigatório"
            toastr.warning(message)
            throw new Error(message)
        }

        if (!this.csrfToken.value) {
            message = "O campo nota é obrigatório"
            toastr.warning(message)
            throw new Error(message)
        }

        showSpinner(btnSubmit)
        const form = new FormData(this)
        form.append("uuid", cashFlowExplanatoryNoteUuid)

        fetch(endpointCashFlowExplanatoryNoteUpdate, {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function(response) {
            btnSubmit.removeAttribute("disabled")
            btnSubmit.innerHTML = "Atualizar"

            if (response.error) {
                message = response.error
                message = message.charAt(0).toUpperCase() + message.slice(1)
                throw new Error(message)
            }

            if (response.success) {
                modal.style.display = "flex"
                setTimeout(() => {
                    window.location.href = response.success
                }, 1000)
            }
        })
    })
}
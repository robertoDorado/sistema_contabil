const explanatoryNotesBalanceSheetEndpoint = window.location.pathname.replace(/\/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/, "")
const explanatoryNotesBalanceSheetUuid = Array.isArray(window.location.pathname.match(/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/)) ?
window.location.pathname.match(/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/)[0] : ""

if (explanatoryNotesBalanceSheetEndpoint == "/admin/balance-sheet-explanatory-notes/form/update") {
    document.getElementById("balanceSheetExplanatoryNotesForm").addEventListener("submit", function (event) {
        event.preventDefault()
        let message = ""
        const btnSubmit = this.querySelector("[type='submit']")
        
        if (!this.explanatoryNoteText.value) {
            message = "O campo nota explicativa não pode estar vazio"
            toastr.warning(message)
            throw new Error(message)
        }
        
        if (!this.csrfToken.value) {
            message = "O campo csrfToken não pode estar vazio"
            toastr.warning(message)
            throw new Error(message)
        }

        showSpinner(btnSubmit)
        const form = new FormData(this)
        form.append("uuid", explanatoryNotesBalanceSheetUuid)

        fetch(window.location.origin + explanatoryNotesBalanceSheetEndpoint, {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function(response) {
            btnSubmit.removeAttribute("disabled")
            btnSubmit.innerHTML = "Atualizar"

            if (response.error) {
                message = response.error
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.error(message)
                throw new Error(message)
            }

            if (response.success) {
                modal.style.display = 'flex'
                setTimeout(() => {
                    window.location.href = response.url
                }, 2000)
            }
        })
    })
}

if (window.location.pathname == "/admin/cash-flow-explanatory-notes/form") {
    const cashFlowSelectMultiple = $('#cashFlowSelectMultiple')
    $(document).ready(function () {
        cashFlowSelectMultiple.select2({
            placeholder: "Selecione as contas do caixa",
            allowClear: true
        });
    });

    document.getElementById("cashFlowExplanatoryNotesForm").addEventListener("submit", function (event) {
        event.preventDefault();
        let message = ""
        const btnSubmit = this.querySelector("[type='submit']")

        if (!this.explanatoryNoteText.value) {
            message = "O campo nota não pode estar vazio"
            toastr.warning(message);
            throw new Error(message)
        }

        if (Array.isArray(cashFlowSelectMultiple.val())) {
            if (cashFlowSelectMultiple.val().length == 0) {
                message = "O campo contas não pode estar vazio"
                toastr.warning(message);
                throw new Error(message)
            }
        }

        const form = new FormData(this)
        showSpinner(btnSubmit)
        const url = window.location.origin + window.location.pathname

        fetch(url,{
            method: "POST",
            body: form
        }).then(response => response.json()).then(function (response) {
            btnSubmit.removeAttribute("disabled")
            btnSubmit.innerHTML = "Enviar"
            cashFlowSelectMultiple.val(null).trigger("change")
            
            if (response.error) {
                message = response.error
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.error(message);
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
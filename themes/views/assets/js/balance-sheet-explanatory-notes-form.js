if (window.location.pathname == "/admin/balance-sheet-explanatory-notes/form") {
    const balanceSheetMultiple = $('#balanceSheetSelectMultiple')
    $(document).ready(function () {
        balanceSheetMultiple.select2({
            placeholder: "Selecione as contas do balanço patrimonial",
            allowClear: true
        });
    });

    document.getElementById("balanceSheetExplanatoryNotesForm").addEventListener("submit", function (event) {
        event.preventDefault();
        let message = ""
        const btnSubmit = this.querySelector("[type='submit']")
        const explanatoryNoteText = this.explanatoryNoteText

        if (!explanatoryNoteText.value) {
            message = "O campo nota não pode estar vazio"
            toastr.warning(message);
            throw new Error(message)
        }

        if (Array.isArray(balanceSheetMultiple.val())) {
            if (balanceSheetMultiple.val().length == 0) {
                message = "O campo contas do balanço patrimonial não pode estar vazio"
                toastr.warning(message);
                throw new Error(message)
            }
        }

        const form = new FormData(this)
        showSpinner(btnSubmit)
        const url = window.location.origin + window.location.pathname

        fetch(url, {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function (response) {
            btnSubmit.removeAttribute("disabled")
            btnSubmit.innerHTML = "Enviar"

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

            const optionsUpdated = response.options_updated
            balanceSheetMultiple.empty()

            optionsUpdated.forEach(function (item) {
                balanceSheetMultiple.append(new Option(item.account_data, item.uuid))
            })

            balanceSheetMultiple.val(null).trigger("change")
            explanatoryNoteText.value = ''
        })
    })
}
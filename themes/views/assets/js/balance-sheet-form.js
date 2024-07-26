if (window.location.pathname == "/admin/balance-sheet/balance-sheet-overview/form") {
    $("#chartOfAccountSelect").select2()
    const createdAt = $("#createdAt")
    const balanceSheetForm = document.getElementById("balanceSheetForm")

    createdAt.datepicker({
        format: "dd/mm/yyyy",
        language: "pt-BR",
        autoclose: true
    });

    $("#accountValue").maskMoney(
        {
            allowNegative: false, 
            thousands:'.', 
            decimal:',', 
            affixesStay: false
        }
    )

    createdAt.on("input", function() {
        let value = $(this).val()
        value = value.replace(/\D/g, '')
        .replace(/(\d{2})(\d)/, "$1/$2")
        .replace(/(\d{2})(\d)/, "$1/$2")
        .replace(/(\/\d{4})\d+?$/, "$1")
        $(this).val(value)
    })

    balanceSheetForm.addEventListener("submit", function(event) {
        event.preventDefault()
        const btnSubmit = this.querySelector("[type='submit']")

        if (!this.accountType.value) {
            toastr.warning("Tipo de lançamento não pode estar vazio")
            throw new Error("Tipo de lançamento não pode estar vazio")
        }

        if (!this.accountHistory.value) {
            toastr.warning("Histórico da conta não pode estar vazio")
            throw new Error("Histórico da conta não pode estar vazio")
        }

        if (!this.accountValue.value) {
            toastr.warning("Valor do lançamento não pode estar vazio")
            throw new Error("Valor do lançamento não pode estar vazio")
        }

        if (!this.createdAt.value) {
            toastr.warning("Data do lançamento não pode estar vazio")
            throw new Error("Data do lançamento não pode estar vazio")
        }

        if (!this.chartOfAccountSelect.value) {
            toastr.warning("Plano de contas não pode estar vazio")
            throw new Error("Plano de contas não pode estar vazio")
        }

        if (!this.csrfToken.value) {
            toastr.warning("Plano de contas não pode estar vazio")
            throw new Error("Plano de contas não pode estar vazio")
        }

        const inputValues = [this.accountType, this.accountHistory, this.accountValue, this.createdAt]
        showSpinner(btnSubmit)
        const form = new FormData(this)

        fetch(window.location.origin + "/admin/balance-sheet/balance-sheet-overview/form", {
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
                message = response.success
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.success(message)
                $("#chartOfAccountSelect").val(null).trigger("change")
                inputValues.forEach(function(element) {
                    element.value = ''
                })
            }
        })
    })
}
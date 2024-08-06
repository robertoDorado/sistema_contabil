const dailyJournalUuid = Array.isArray(window.location.href.match(/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/)) ?
window.location.href.match(/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/)[0] : ""
const dailyJournalEndpoint = window.location.pathname.replace(/\/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/, "")

if (dailyJournalEndpoint == "/admin/balance-sheet/daily-journal/form") {
    $("#chartOfAccountSelect").select2()
    const createdAt = $("#createdAt")
    const accountValue = $("#accountValue")

    createdAt.datepicker({
        format: "dd/mm/yyyy",
        language: "pt-BR",
        autoclose: true
    });

    createdAt.on("input", function() {
        let value = $(this).val()
        value = value.replace(/\D/g, '')
        .replace(/(\d{2})(\d)/, "$1/$2")
        .replace(/(\d{2})(\d)/, "$1/$2")
        .replace(/(\/\d{4})\d+?$/, "$1")
        $(this).val(value)
    })

    accountValue.on("paste", function(event) {
        event.preventDefault()
    })

    accountValue.maskMoney(
        {
            allowNegative: false, 
            thousands:'.', 
            decimal:',', 
            affixesStay: false
        }
    )

    const dailyJournalFormUpdate = document.getElementById("dailyJournalFormUpdate")
    dailyJournalFormUpdate.addEventListener("submit", function(event) {
        event.preventDefault()
        const btnSubmit = this.querySelector("[type='submit']")
        
        if (!this.createdAt.value) {
            toastr.warning("O campo data é obrigatório")
            throw new Error("O campo data é obrigatório")
        }
        
        if (!this.chartOfAccountSelect.value) {
            toastr.warning("O campo nome da conta é obrigatório")
            throw new Error("O campo nome da conta é obrigatório")
        }

        if (!this.accountType.value) {
            toastr.warning("O campo tipo de conta é obrigatório")
            throw new Error("O campo tipo de conta é obrigatório")
        }

        if (!this.accountValue.value) {
            toastr.warning("O campo valor de lançamento é obrigatório")
            throw new Error("O campo valor de lançamento é obrigatório")
        }

        showSpinner(btnSubmit)
        const form = new FormData(this)
        form.append("uuid", dailyJournalUuid)

        fetch(window.location.origin + "/admin/balance-sheet/daily-journal/form", {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function(response) {
            btnSubmit.removeAttribute("disabled")
            btnSubmit.innerHTML = "Atualizar"
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
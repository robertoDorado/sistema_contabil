if (window.location.pathname == "/admin/cash-variation-setting/backup") {
    const dataTransfer = {}
    $("#cashFlowVariationBackup").on("click", "a.restore-icon,a.trash-icon", function (event) {
        event.preventDefault()
        dataTransfer.uuid = $(this).data("uuid")
        dataTransfer.account_name = $(this).data("accountname")
        dataTransfer.csrf = $(this).data("csrf")
        dataTransfer.change_type = Array.isArray(this.classList.value.match(/restore-icon/)) ? "restore" : "delete"
        dataTransfer.row = this.closest("tr")
        $("#launchModal").click()
    })

    $("#launchModal").click(function() {
        $("#modalContainerLabel").html("Atenção!")
        $("#dismissModal").html("Voltar")
        $("#saveChanges").removeClass("btn-primary")
        $("#saveChanges").removeClass("btn-danger")
        
        if (dataTransfer.change_type == "restore") {
            $(".modal-body").html(`Você deseja restaurar a conta "${dataTransfer.account_name}"?`)
            $("#saveChanges").addClass("btn-primary")
            $("#saveChanges").html("Restaurar")
        }else {
            $(".modal-body").html(`Você deseja excluir permanentemente a conta "${dataTransfer.account_name}"?`)
            $("#saveChanges").addClass("btn-danger")
            $("#saveChanges").html("Excluir")
        }
    })

    $("#saveChanges").click(function() {
        const saveChanges = this
        showSpinner(saveChanges)
        
        const form = new FormData()
        form.append("csrfToken", dataTransfer.csrf)
        form.append("changeType", dataTransfer.change_type)
        form.append("uuid", dataTransfer.uuid)

        const url = window.location.origin + window.location.pathname
        fetch(url, {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function(response) {
            saveChanges.removeAttribute("disabled")
            saveChanges.innerHTML = dataTransfer.change_type == "restore" ? "Restaurar" : "Excluir"
            let message = ""

            if (response.error) {
                message = response.error
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.error(message)
                throw new Error(message)
            }

            if (response.success) {
                cashFlowVariationBackup.row(dataTransfer.row).remove().draw(false)
                $("#dismissModal").click()
            }
        })
    })
}
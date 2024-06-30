if (window.location.pathname == "/admin/cash-variation-setting/backup") {
    const dataTranfer = {}
    $("#cashFlowVariationBackup").on("click", "a.restore-icon,a.trash-icon", function (event) {
        event.preventDefault()
        dataTranfer.uuid = $(this).data("uuid")
        dataTranfer.account_name = $(this).data("accountname")
        dataTranfer.csrf = $(this).data("csrf")
        dataTranfer.change_type = Array.isArray(this.classList.value.match(/restore-icon/)) ? "restore" : "delete"
        dataTranfer.row = this.closest("tr")
        $("#launchModal").click()
    })

    $("#launchModal").click(function() {
        $("#modalContainerLabel").html("Atenção!")
        $("#dismissModal").html("Voltar")
        $("#saveChanges").removeClass("btn-primary")
        $("#saveChanges").removeClass("btn-danger")
        
        if (dataTranfer.change_type == "restore") {
            $(".modal-body").html(`Você deseja restaurar a conta "${dataTranfer.account_name}"?`)
            $("#saveChanges").addClass("btn-primary")
            $("#saveChanges").html("Restaurar")
        }else {
            $(".modal-body").html(`Você deseja excluir permanentemente a conta "${dataTranfer.account_name}"?`)
            $("#saveChanges").addClass("btn-danger")
            $("#saveChanges").html("Excluir")
        }
    })

    $("#saveChanges").click(function() {
        const saveChanges = this
        showSpinner(saveChanges)
        
        const form = new FormData()
        form.append("csrfToken", dataTranfer.csrf)
        form.append("changeType", dataTranfer.change_type)
        form.append("uuid", dataTranfer.uuid)

        const url = window.location.origin + window.location.pathname
        fetch(url, {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function(response) {
            saveChanges.removeAttribute("disabled")
            saveChanges.innerHTML = dataTranfer.change_type == "restore" ? "Restaurar" : "Excluir"
            let message = ""

            if (response.error) {
                message = response.error
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.error(message)
                throw new Error(message)
            }

            if (response.success) {
                cashFlowVariationBackup.row(dataTranfer.row).remove().draw()
                $("#dismissModal").click()
            }
        })
    })
}
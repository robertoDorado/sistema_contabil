if (window.location.pathname == "/admin/cash-flow-explanatory-notes/report") {
    const dataTransfer = {
        uuid: "",
        accountName: "",
        row: null
    }

    const saveChanges = $("#saveChanges")
    cashFlowExplanatoryNotesReport.on("click", "[trash-icon]", function(event) {
        event.preventDefault()
        dataTransfer.uuid = $(this).data("uuid")
        dataTransfer.accountName = $(this).data("accountname")
        dataTransfer.row = $(this).closest("tr")
        $("#launchModal").click()
    })

    $("#launchModal").click(function() {
        $("#modalContainerLabel").html("Atenção!")
        $(".modal-body").html(`Você tem certeza que deseja remover a conta "${dataTransfer.accountName}"?`)
        saveChanges.removeClass("btn-primary")
        saveChanges.addClass("btn-danger")
        saveChanges.html("Excluir")
    })

    saveChanges.click(function() {
        const saveChangeBtn = this
        showSpinner(saveChangeBtn)

        const form = new FormData()
        form.append("uuid", dataTransfer.uuid)
        const url = window.location.origin + "/admin/cash-flow-explanatory-notes/remove"
        
        fetch(url, {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function(response) {
            $(saveChangeBtn).removeAttr("disabled")
            $(saveChangeBtn).html("Excluir")
            $("#dismissModal").click()
            let message = ""
            
            if (response.error) {
                message = response.error
                message = message.charAt(0).toUpperCase() + message.slice(1)
                throw new Error(message)
            }

            if (response.success) {
                message = response.success
                message = message.charAt(0).toUpperCase() + message.slice(1)
                cashFlowExplanatoryNotesReport.row(dataTransfer.row).remove().draw()
            }
        })
    })
}
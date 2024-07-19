if (window.location.pathname == "/admin/cash-variation-setting/report") {
    const dataTransfer = {
        uuid: ""
    }
    
    $("#cashFlowVariation").on("click", "a.trash-icon", function(event) {
        event.preventDefault()
        dataTransfer.row = $(this).closest("tr")[0]
        dataTransfer.uuid = $(this).data("uuid")
        dataTransfer.csrf = $(this).data("csrf")
        dataTransfer.nameReference = $(this).data("accountname")
        $("#launchModal").click()
    })
    
    $("#launchModal").click(function() {
        $("#modalContainerLabel").html(`Atenção!`)
        $(".modal-body").html(`Deseja mesmo excluir o registro "${dataTransfer.nameReference}"?`)
        $("#dismissModal").html("Sair")
        $("#saveChanges").removeClass("btn-primary")
        $("#saveChanges").addClass("btn-danger")
        $("#saveChanges").html("Excluir")
    })

    $("#saveChanges").click(function() {
        showSpinner(this)
        const form = new FormData()

        form.append("uuid", dataTransfer.uuid)
        form.append("csrfToken", dataTransfer.csrf)

        fetch("/admin/cash-variation-setting/remove", {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function(response) {
            $("#dismissModal").click()
            $("#saveChanges").removeAttr("disabled")
            $("#saveChanges").html("Excluir")
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
                cashFlowVariation.row(dataTransfer.row).remove().draw(false)
            }
        })
    })
}
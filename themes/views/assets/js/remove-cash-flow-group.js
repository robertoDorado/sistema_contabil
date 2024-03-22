if (window.location.pathname == '/admin/cash-flow-group/report') {
    const trashIconBtn = Array.from(document.querySelectorAll(".fa.fa-trash"))
    if (trashIconBtn) {
        const launchModal = document.getElementById("launchModal")
        const modalContainerLabel = document.getElementById("modalContainerLabel")
        const modalBody = document.querySelector(".modal-body")
        const saveChanges = document.getElementById("saveChanges")
        
        saveChanges.classList.remove("btn-primary")
        saveChanges.classList.add("btn-danger")
        saveChanges.innerHTML = "Excluir"

        const dismissModal = document.getElementById("dismissModal")
        dismissModal.innerHTML = "Voltar"

        const dataDelete = {}
        trashIconBtn.forEach(function(element) {
            const linkDelete = element.parentElement
            linkDelete.addEventListener("click", function (event) {
                event.preventDefault()

                const row = this.parentElement.parentElement
                let uuidParameter = this.parentElement.previousElementSibling.firstElementChild
                
                uuidParameter = uuidParameter.href.split("/")
                uuidParameter = uuidParameter.pop()
                let url = `${window.location.origin}/admin/cash-flow-group/remove/${uuidParameter}`

                dataDelete.uuidParameter = uuidParameter
                dataDelete.url = url
                dataDelete.row = row
                launchModal.click()
            })
        })

        launchModal.addEventListener("click", function() {
            modalContainerLabel.innerHTML = "Atenção!"
            modalBody.innerHTML = `Você quer mesmo deletar o registro ${dataDelete.uuidParameter}?`
        })
        
        saveChanges.addEventListener("click", function() {
            showSpinner(saveChanges)
            fetch(`${window.location.origin}/admin/cash-flow-group/remove/${dataDelete.uuidParameter}`,
            { method: "POST" })
            .then((response) => response.json()).then(function(response) {
                let message = ""
                
                if (response.error) {
                    saveChanges.innerHTML = "Excluir"
                    message = response.error
                    message = message.charAt(0).toLocaleUpperCase() + message.slice(1)
                    toastr.error(message)
                    throw new Error(message)
                }

                if (response.success) {
                    message = response.success
                    message = message.charAt(0).toUpperCase() + message.slice(1)
                    toastr.success(message)
                    cashFlowGroupTable.row(dataDelete.row).remove().draw()
                    saveChanges.innerHTML = "Excluir"
                    dismissModal.click()
                }
            })
        })
    }
}
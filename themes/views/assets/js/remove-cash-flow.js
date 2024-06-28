if (window.location.pathname == '/admin/cash-flow/report') {
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
                const row = this.closest("tr")
                let uuidParameter = this.closest("td").previousElementSibling.firstElementChild
                
                uuidParameter = uuidParameter.href.split("/")
                uuidParameter = uuidParameter.pop()
                let url = `${window.location.origin}/admin/cash-flow/remove/${uuidParameter}`

                dataDelete.uuidParameter = uuidParameter
                dataDelete.url = url
                dataDelete.row = row
                dataDelete.accountReference = Array.from(row.children)[3].innerHTML
                launchModal.click()
            })
        })

        launchModal.addEventListener("click", function() {
            modalContainerLabel.innerHTML = "Atenção!"
            modalBody.innerHTML = `Você quer mesmo deletar o registro "${dataDelete.accountReference}"?`
        })

        saveChanges.addEventListener("click", function() {
            showSpinner(saveChanges)
            fetch(`${window.location.origin}/admin/cash-flow/remove/${dataDelete.uuidParameter}`,
            { method: "POST" })
            .then((response) => response.json()).then(function(response) {
                let message = ""
                const tFoot = Array.from(document.querySelector("tfoot").firstElementChild.children)
                const totalRow = document.querySelector("tfoot").firstElementChild
                totalRow.style.color = response.color
                saveChanges.innerHTML = "Excluir"
                saveChanges.removeAttribute("disabled")

                tFoot.forEach(function(element) {
                    if (element.innerHTML && element.innerHTML != 'Total') {
                        element.innerHTML = response.balance
                    }
                })

                if (response.data_is_empty) {
                    message = response.data_is_empty
                    message = message.charAt(0).toUpperCase() + message.slice(1)
                    toastr.error(message)
                    throw new Error(message)
                }

                if (response.cash_flow_data_not_found) {
                    message = response.cash_flow_data_not_found
                    message = message.charAt(0).toUpperCase() + message.slice(1)
                    toastr.error(message)
                    throw new Error(message)
                }

                if (response.error) {
                    message = response.error
                    message = message.charAt(0).toUpperCase() + message.slice(1)
                    toastr.error(message)
                    throw new Error(message)
                }

                if (response.success) {
                    message = response.message
                    message = message.charAt(0).toUpperCase() + message.slice(1)
                    toastr.success(message)
                    cashFlowTable.row(dataDelete.row).remove().draw()
                    dismissModal.click()
                }
            })
        })
    }
}
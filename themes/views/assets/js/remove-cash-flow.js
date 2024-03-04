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

                const row = this.parentElement.parentElement
                let uuidParameter = this.parentElement.previousElementSibling.firstElementChild
                
                uuidParameter = uuidParameter.href.split("/")
                uuidParameter = uuidParameter.pop()
                
                let uuidReference = uuidParameter.split("-")
                uuidReference = uuidReference.shift()
                let url = `${window.location.origin}/admin/cash-flow/remove/${uuidParameter}`

                dataDelete.uuidParameter = uuidParameter
                dataDelete.url = url
                dataDelete.uuidReference = uuidReference
                dataDelete.row = row
                launchModal.click()
            })
        })

        launchModal.addEventListener("click", function() {
            modalContainerLabel.innerHTML = "Atenção!"
            modalBody.innerHTML = `Você quer mesmo deletar o registro ${dataDelete.uuidReference}?`
            
            saveChanges.addEventListener("click", function() {
                showSpinner(saveChanges)
                fetch(`${window.location.origin}/admin/cash-flow/remove/${dataDelete.uuidParameter}`,
                { method: "POST" })
                .then((response) => response.json()).then(function(response) {
                    
                    const tFoot = Array.from(document.querySelector("tfoot").firstElementChild.children)
                    const totalRow = document.querySelector("tfoot").firstElementChild
                    totalRow.style.color = response.color

                    tFoot.forEach(function(element) {
                        if (element.innerHTML && element.innerHTML != 'Total') {
                            element.innerHTML = response.balance
                        }
                    })

                    if (response.success) {
                        dataDelete.row.style.display = "none"
                        saveChanges.innerHTML = "Excluir"
                        dismissModal.click()
                    }
                })
            })
        })
    }
}
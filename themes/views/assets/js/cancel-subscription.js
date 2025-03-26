if (window.location.pathname == "/admin/customer/cancel-subscription/form") {
    const cancelSubscriptionForm = document.getElementById("cancelSubscriptionForm")
    cancelSubscriptionForm.addEventListener("submit", function (event) {
        event.preventDefault()

        if (!this.cancelSubscriptionValue.value) {
            toastr.error("Campo de cancelamento de assinatura é obrigatório")
            throw new Error("Campo de cancelamento de assinatura é obrigatório")
        }

        if (!this.csrfToken.value) {
            toastr.error("Campo token é obrigatório")
            throw new Error("Campo token é obrigatório")
        }

        const btnSubmit = this.querySelector("[type='submit']")
        showSpinner(btnSubmit)
        
        const form = new FormData(this)
        form.append("cancelData", true)

        fetch("/customer/subscription/cancel-subscription", {
            method: "POST",
            body: form
        }).then(response => response.json()).then((response) => {
            btnSubmit.removeAttribute("disabled")
            btnSubmit.innerHTML = "Cancelar assinatura"
            let message = ""

            if (response.error) {
                message = response.error
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.error(message)
                throw new Error(message)
            }

            if (response.success) {
                this.cancelSubscriptionValue.value = ""
                message = response.success
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.success(message)
            }
        })
    })
}

// const cancelSubscription = document.querySelector("[cancelSubscription]")
// const saveChanges = document.getElementById("saveChanges")

// const dismissModal = document.getElementById("dismissModal")
// let cancelBtnTitle = "Cancelar a minha assinatura"

// cancelSubscription.addEventListener("click", function() {
//     const launchModal = document.getElementById("launchModal")
//     launchModal.click()

//     const modalContainerLabel = document.getElementById("modalContainerLabel")
//     const modalBody = document.querySelector(".modal-body")

//     saveChanges.classList.remove("btn-primary")
//     saveChanges.classList.add("btn-danger")

//     modalContainerLabel.innerHTML = "Cancelamento da assinatura"
//     modalBody.innerHTML = "Deseja mesmo cancelar a sua assinatura permanentemente?"
//     dismissModal.innerHTML = "Não cancelar"
//     saveChanges.innerHTML = cancelBtnTitle
// })

// saveChanges.addEventListener("click", function () {
//     const btnCancelSubscription = this
//     showSpinner(btnCancelSubscription)

//     const form = new FormData()
//     form.append("cancelData", true)

//     fetch(window.location.origin + "/customer/subscription/cancel-subscription", {
//         method: "POST",
//         body: form
//     }).then(response => response.json()).then(function (response) {
//         let message = ""

//         if (response.error) {
//             dismissModal.click()
//             btnCancelSubscription.removeAttribute("disabled")
//             btnCancelSubscription.innerHTML = cancelBtnTitle
//             message = response.error
//             message = message.charAt(0).toUpperCase() + message.slice(1)
//             toastr.error(message)
//             throw new Error(message)
//         }

//         if (response.success) {
//             dismissModal.click()
//             btnCancelSubscription.removeAttribute("disabled")
//             btnCancelSubscription.innerHTML = cancelBtnTitle
//             message = response.success
//             message = message.charAt(0).toUpperCase() + message.slice(1)
//             toastr.success(message)
//         }
//     })
// })
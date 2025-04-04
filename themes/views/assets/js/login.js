if (window.location.pathname == "/admin/login") {
    const passwordToggle = document.getElementById("passwordToggle")
    passwordToggle.addEventListener("click", callbackTogglePassword)
    
    const loginForm = document.getElementById("loginForm")
    loginForm.addEventListener("submit", function(event) {
        event.preventDefault()

        const btnSubmit = this.querySelector(".btn.btn-primary.btn-block")
        if (!this.userData.value) {
            toastr.warning("Campo nome de usuário deve ser obrigatório")
            throw new Error("Campo nome de usuário deve ser obrigatório")
        }

        if (!this.userPassword.value) {
            toastr.warning("Campo senha deve ser obrigatório")
            throw new Error("campo senha deve ser obrigatório")
        }

        if (!this.csrfToken.value) {
            toastr.warning("Campo csrf-token inválido")
            throw new Error("Campo csrf-token inválido")
        }

        if (!this.userType.value) {
            toastr.warning("Campo tipo de usuário inválido")
            throw new Error("Campo tipo de usuário inválido")
        }

        showSpinner(btnSubmit)
        const form = new FormData(this)
        fetch(window.location.pathname, {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function(response) {
            let message = ''

            if (response.error) {
                message = response.error
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.error(message)
                btnSubmit.innerHTML = 'Login'
                btnSubmit.removeAttribute("disabled")
                throw new Error(message)
            }

            if (response.login_success) {
                window.location.href = response.url
            }
        })
    })
}
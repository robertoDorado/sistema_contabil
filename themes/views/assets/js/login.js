if (window.location.pathname == "/admin/login") {
    const loginForm = document.getElementById("loginForm")
    loginForm.addEventListener("submit", function(event) {
        event.preventDefault()

        if (this.userEmail.value == '') {
            toastr.error("Campo e-mail deve ser obrigatório")
            throw new Error("Campo e-mail deve ser obrigatório")
        }

        if (!this.userEmail.value.match(/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/)) {
            toastr.error("Este e-mail não é válido")
            throw new Error("este e-mail não é válido")
        }

        if (this.userPassword.value == '') {
            toastr.error("Campo senha deve ser obrigatório")
            throw new Error("campo senha deve ser obrigatório")
        }

        if (this.csrfToken.value == '') {
            toastr.error("Campo csrf-token inválido")
            throw new Error("Campo csrf-token inválido")
        }

        const form = new FormData(this)
        fetch(window.location.pathname, {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function(response) {
            let message = ''
            if (response.invalid_login_data) {
                message = response.invalid_login_data
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.error(message)
                throw new Error(message)
            }

            if (response.user_not_register) {
                message = response.user_not_register
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.error(message)
                throw new Error(message)
            }

            if (response.user_not_auth) {
                message = response.user_not_auth
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.error(message)
                throw new Error(message)
            }

            console.log(response)
        })
    })
}
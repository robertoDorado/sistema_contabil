if (window.location.pathname == "/customer/subscribe") {
    $(document).ready(function () {
        $('[name="monthYearPicker"]').datepicker({
            format: "mm/yyyy",
            startView: "months",
            minViewMode: "months",
            language: "pt-BR",
            autoclose: true
        });

        $("[name='birthDate']").datepicker({
            format: "dd/mm/yyyy",
            language: "pt-BR",
            autoclose: true
        });
    });

    const verifyDocument = {
        "14": function(value) {
            return value.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/, "$1.$2.$3/$4-$5")
        },

        "11": function(value) {
            return value.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})$/, "$1.$2.$3-$4")
        }
    }

    const documentData = document.querySelector("[name='document']")
    documentData.addEventListener("input", function() {
        this.value = this.value.replace(/\D/g, "")
        if (typeof verifyDocument[this.value.length] == "function") {
            this.value = verifyDocument[this.value.length](this.value)
        }

        if (this.value.length >= 14) {
            this.maxLength = 18
        }
    })

    const birthDate = document.querySelector("[name='birthDate']")
    birthDate.addEventListener("input", function() {
        this.value = this.value.replace(/\D/g, "")
        .replace(/(\d{2})(\d)/, "$1/$2")
        .replace(/(\d{2})(\d)/, "$1/$2")
        .replace(/(\/\d{4})\d+?$/, "$1")
    })

    const zipcode = document.querySelector("[name='zipcode']")
    zipcode.addEventListener("input", function() {
        this.value = this.value.replace(/(\d{5})(\d)/, "$1-$2")
        .replace(/(-\d{3})\d+?$/, "$1")

        const searchValue = this.value.replace(/[^\d]+/, "")
        if (searchValue.length >= 8) {
            fetch(`https://brasilapi.com.br/api/cep/v1/${searchValue}`)
            .then(response => response.json()).then(function(response) {
                if (response.cep) {
                    document.querySelector('[name="address"]').value = response.street
                    document.querySelector('[name="neighborhood"]').value = response.neighborhood
                    document.querySelector('[name="city"]').value = response.city
                    document.querySelector('[name="state"]').value = response.state
                }
            })
        }
    })

    const phone = document.querySelector("[name='phone']")
    phone.addEventListener("input", function() {
        this.value = this.value
        .replace(/\D/g, "")
        .replace(/(\d{2})(\d)/, "($1) $2")
        .replace(/(\d{4})(\d)/, "$1-$2")
        .replace(/(-\d{4})\d+?$/, "$1")
    })

    const cellPhone = document.querySelector("[name='cellPhone']")
    cellPhone.addEventListener("input", function() {
        this.value = this.value
        .replace(/\D/g, "")
        .replace(/(\d{2})(\d)/, "($1) $2")
        .replace(/(\d{5})(\d)/, "$1-$2")
        .replace(/(-\d{4})\d+?$/, "$1")
    })

    const stripe = Stripe("pk_test_51OEIojC1Uv10wqUudCsaCYGleVine1HcYMo3kLbOJDbFnetTHFMLkCEiCt24J256ahte6UCvHkBfFMrlEIT7qFlE00LQx8SDKD", {
        locale: "pt-BR"
    })

    const elements = stripe.elements()
    const style = {
        base: {
            fontSize: '16px',
            fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
            fontSmoothing: 'antialiased',
            color: '#555',
            '::placeholder': {
                color: '#999'
            }
        },
        invalid: {
            color: '#fa755a',
            iconColor: '#fa755a'
        }
    }

    const card = elements.create('card', { 
        style: style 
    })

    const cardMount = document.getElementById("cardMount")
    card.mount(cardMount)

    const subscriptionForm = document.getElementById("subscriptionForm")
    subscriptionForm.addEventListener("submit", function(event) {
        event.preventDefault()

        const btnSubmit = this.querySelector("button[type='submit']")
        if (/\s/.test(this.userName.value)) {
            toastr.warning("Nome de usuário não pode conter espaços em branco")
            throw new Error("Nome de usuário não pode conter espaços em branco")
        }

        let validateBlankInput = Array.from(this.getElementsByTagName("input"))
        validateBlankInput = validateBlankInput.filter(function(element) {
            if (!element.classList.contains("__PrivateStripeElement-input")
            && element.name != "phone" && element.name != "cellPhone") {
                return element
            }
        })
        
        validateBlankInput.forEach(function(element) {
            if (!element.value) {
                toastr.warning(`Campos obrigatórios não foram preenchidos`)
                throw new Error(`Campos obrigatórios não foram preenchidos`)
            }
        })

        const selectField = this.querySelector("select")
        if (!selectField.value) {
            toastr.warning(`Campos obrigatórios não foram preenchidos`)
            throw new Error(`Campos obrigatórios não foram preenchidos`)
        }

        if (this.password.value != this.confirmPassword.value) {
            toastr.warning("As senhas não conferem")
            throw new Error("As senhas não conferem")
        }

        const form = new FormData(this)
        showSpinner(btnSubmit)

        stripe.createToken(card).then(function(response) {
            if (response.error) {
                toastr.error(`Erro ao processar cartão de crédito: ${response.error.message}`)
                throw new Error("Erro ao processar cartão de crédito")
            }
            
            form.append("cardToken", response.token.id)
            fetch(window.location.origin + "/customer/subscription/process-payment", {
                method: "POST",
                body: form
            }).then(response => response.json()).then(function(response) {

                if (response.error) {
                    btnSubmit.innerHTML = "Comprar assinatura"
                    btnSubmit.removeAttribute("disabled")
                    let message = response.error
                    message = message.charAt(0).toUpperCase() + message.slice(1)
                    toastr.error(message)
                    throw new Error(message)
                }

                if (response.success) {
                    window.location.href = response.url
                }
            })
        })
    })
}
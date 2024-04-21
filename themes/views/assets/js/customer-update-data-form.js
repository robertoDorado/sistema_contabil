if (window.location.pathname == "/admin/customer/update-data/form") {
    $(document).ready(function () {
        $("[name='birthDate']").datepicker({
            format: "dd/mm/yyyy",
            language: "pt-BR",
            autoclose: true
        });
    });

    const maskDocument = {
        "11": function(value) {
            return value.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})$/, "$1.$2.$3-$4")
        },
        "14": function(value) {
            return value.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/, "$1.$2.$3/$4-$5")
        }
    }

    const customerDocument = document.querySelector("[name='document']")
    customerDocument.addEventListener("input", function() {
        this.value = this.value.replace(/\D/g, "")
        if (typeof maskDocument[this.value.length] == "function") {
            this.value = maskDocument[this.value.length](this.value)
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
        this.value = this.value.replace(/\D/g, "").replace(/(\d{5})(\d)/, "$1-$2")
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

    const addressNumber = document.querySelector("[name='number']")
    addressNumber.addEventListener("input", function() {
        this.value = this.value.replace(/\D/g, "")
    })

    const state = document.querySelector("[name='state']")
    state.addEventListener("input", function() {
        this.value = this.value.replace(/[^A-Za-z]+/g, '')
        .toUpperCase().replace(/([A-Z]{2})[A-Z]+?$/, "$1")
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
}
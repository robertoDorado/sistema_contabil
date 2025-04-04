const modal = document.getElementById("loadingModal");

function callbackTogglePassword() {
    const eyeIcon = this.firstElementChild
    const inputElement = this.parentElement.previousElementSibling

    if (eyeIcon.classList.contains("fa-eye-slash")) {
        eyeIcon.classList.remove("fa-eye-slash")
        eyeIcon.classList.add("fa-eye")
        inputElement.type = "text"
    } else {
        eyeIcon.classList.remove("fa-eye")
        eyeIcon.classList.add("fa-eye-slash")
        inputElement.type = "password"
    }
}

function stripePublicKeys() {
    return {
        live: "pk_live_51OEIojC1Uv10wqUugUxFvBmy3CWhpFjR9t9lR9trtxfdxgKWdnQxzUERnlysdy1USdCfRTvUq72pBIAKNPH9V3tj00COXqcXEt",
        test: "pk_test_51OEIojC1Uv10wqUudCsaCYGleVine1HcYMo3kLbOJDbFnetTHFMLkCEiCt24J256ahte6UCvHkBfFMrlEIT7qFlE00LQx8SDKD"
    }
}

function downloadRequestPost(response, fileName) {
    const url = window.URL.createObjectURL(response)
    const a = document.createElement('a');
    a.style.display = 'none';
    a.href = url;
    a.download = fileName;
    document.body.appendChild(a);
    a.click();
    window.URL.revokeObjectURL(url);
}

function companyMaskForm() {
    return {
        cnpj: function (value) {
            return value.replace(/\D/g, "")
                .replace(/(\d{2})(\d)/, "$1.$2")
                .replace(/(\d{3})(\d)/, "$1.$2")
                .replace(/(\d{3})(\d)/, "$1/$2")
                .replace(/(\d{4})(\d)/, "$1-$2")
                .replace(/(-\d{2})\d+?$/, "$1")
        },

        cpf: function (value) {
            return value.replace(/\D/g, "")
                .replace(/(\d{3})(\d)/, "$1.$2")
                .replace(/(\d{3})(\d)/, "$1.$2")
                .replace(/(\d{3})(\d)/, "$1-$2")
                .replace(/(-\d{2})\d+?$/, "$1")
        },

        cep: function (value) {
            return value.replace(/\D/g, "")
                .replace(/(\d{5})(\d)/, "$1-$2")
                .replace(/(-\d{3})\d+?$/, "$1")
        },

        number: function (value) {
            return value.replace(/\D/g, "")
        },

        phone: function (value) {
            return value.replace(/\D/g, "")
                .replace(/(\d{2})(\d)/, "($1) $2")
                .replace(/(\d{4})(\d)/, "$1-$2")
                .replace(/(-\d{4})\d+?$/, "$1")
        },

        cellPhone: function (value) {
            return value.replace(/\D/g, "")
                .replace(/(\d{2})(\d)/, "($1) $2")
                .replace(/(\d{5})(\d)/, "$1-$2")
                .replace(/(-\d{4})\d+?$/, "$1")
        },

        date: function (value) {
            return value.replace(/\D/g, "")
                .replace(/(\d{2})(\d)/, "$1/$2")
                .replace(/(\d{2})(\d)/, "$1/$2")
                .replace(/(\/\d{4})\d+?$/, "$1")
        },

        state: function (value) {
            return value.replace(/[^A-Za-z]+/g, '')
                .toUpperCase().replace(/([A-Z]{2})[A-Z]+?$/, "$1")
        },

        inscricaoEstadual: function (value) {
            return value.replace(/\D/g, "")
                .replace(/(\d{3})(\d)/, "$1.$2")
                .replace(/(\d{3})(\d)/, "$1.$2")
                .replace(/(\d{3})(\d)/, "$1.$2")
                .replace(/(\.\d{3})\d+?$/, "$1")
        },

        invoiceSeries: function (value) {
            return value.replace(/\D/g, "")
                .replace(/(\d{3})\d+?$/, "$1")
        },

        invoiceNumber: function (value) {
            return value.replace(/\D/g, "")
                .replace(/(\d{9})\d+?$/, "$1")
        },

        CNAE: function (value) {
            return value.replace(/\D/g, "")
        },

        cfop: function (value) {
            return value.replace(/\D/g, "")
                .replace(/(\d{4})\d+?$/, "$1")
        }
    }
}

function formatDate(inputDate) {
    const [day, month, year] = inputDate.split('/');
    const formattedDate = `${year}-${month}-${day}`;
    return formattedDate
}

function extensionFileName(value) {
    return value.split(".").pop().toLowerCase()
}

function dataTableConfig(jQuerySelector, objectConfigDataTable = {}) {
    return jQuerySelector.DataTable(objectConfigDataTable)
}

function showSpinner(btn) {
    const spinner = document.createElement("i")
    spinner.classList.add("fas", "fa-spinner", "fa-spin")
    btn.setAttribute("disabled", "")
    btn.innerHTML = ''
    btn.appendChild(spinner)
}
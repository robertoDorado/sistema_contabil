const modal = document.getElementById("loadingModal");
function customMatcher(params, data) {
    // Se não há termo de busca, retorne todos os dados
    if ($.trim(params.term) === '') {
        return data;
    }

    // Converta para minúsculas para fazer uma busca case-insensitive
    if (typeof data.text === 'undefined') {
        return null;
    }

    var term = params.term.toLowerCase();
    var text = data.text.toLowerCase();

    // Verifique se o termo de busca está contido no texto
    if (text.indexOf(term) > -1) {
        return data;
    }

    // Retorne null se não houver correspondência
    return null;
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
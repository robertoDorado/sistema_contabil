function extensionFileName(value) {
    return value.split(".").pop().toLowerCase()
}

function dataTableConfig(jQuerySelector, objectConfigDataTable = {}) {
    return jQuerySelector.DataTable(objectConfigDataTable)
}

function showSpinner(btn) {
    const spinner = document.createElement("i")
    spinner.classList.add("fas", "fa-spinner", "fa-spin")
    btn.innerHTML = ''
    btn.appendChild(spinner)
}
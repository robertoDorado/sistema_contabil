const modal = document.getElementById("loadingModal");
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
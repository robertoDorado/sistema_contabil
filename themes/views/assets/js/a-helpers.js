function showSpinner(btn) {
    const spinner = document.createElement("i")
    spinner.classList.add("fas", "fa-spinner", "fa-spin")
    btn.innerHTML = ''
    btn.appendChild(spinner)
}
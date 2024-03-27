if (window.location.pathname + window.location.search == `/admin/cash-flow/report${window.location.search}`) {
    const searchCashFlowById = document.getElementById("searchCashFlowById")
    searchCashFlowById.addEventListener("submit", function(event) {
        event.preventDefault()
        const btnSubmit = this.querySelector("[type='submit']")
        showSpinner(btnSubmit)
        this.submit()
    })
}
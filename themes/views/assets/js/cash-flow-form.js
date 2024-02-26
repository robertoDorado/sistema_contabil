if (window.location.pathname == '/admin/cash-flow/form') {
    const cashFlowForm = document.getElementById("cashFlowForm")
    $("#launchValue").maskMoney(
        {
            allowNegative: false, 
            thousands:'.', 
            decimal:',', 
            affixesStay: false
        }
    )
    cashFlowForm.addEventListener("submit", function(event) {
        event.preventDefault()
        const form = new FormData(this)

        fetch(window.location.href, 
        {
            method: "POST",
            body: form
        }
        ).then((response) => response.json())
        .then(function(response) {
            console.log(response)
        })
    })
}
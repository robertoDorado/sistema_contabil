if (window.location.pathname == "/admin/cash-variation-setting/report") {
    document.getElementById("accountGroupVariation").addEventListener("change", function() {
        const form = new FormData()
        form.append("accountGroupVariation", this.value)
        
        const url = window.location.origin + window.location.pathname
        modal.style.display = "flex"
        fetch(url, {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function(response) {
            if (response.success) {
                window.location.href = url
            }
        })
    })
}
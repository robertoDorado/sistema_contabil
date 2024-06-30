const cashVariationReportEndpoints = [
    "/admin/cash-variation-setting/backup",
    "/admin/cash-variation-setting/report"
];

if (cashVariationReportEndpoints.indexOf(window.location.pathname) != -1) {
    document.getElementById("accountGroupVariation").addEventListener("change", function() {
        const form = new FormData()
        form.append("accountGroupVariation", this.value)
        
        const url = window.location.origin + window.location.pathname
        modal.style.display = "flex"
        fetch("/admin/cash-variation-setting/report", {
            method: "POST",
            body: form
        }).then(response => response.json()).then(function(response) {
            if (response.success) {
                window.location.href = url
            }
        })
    })
}
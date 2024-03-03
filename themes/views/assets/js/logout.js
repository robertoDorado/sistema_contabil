const logoutBtn = document.getElementById("logout")
logoutBtn.addEventListener("click", function (event) {
    event.preventDefault()

    const form = new FormData()
    form.append('request', JSON.stringify({ logout: true }))

    fetch(window.location.origin + "/admin/logout",
        {
            method: "POST",
            body: form
        }
    ).then((response) => response.json())
        .then(function (response) {
            if (response.logout_success) {
                window.location.href = window.location.href
            }
        })
})
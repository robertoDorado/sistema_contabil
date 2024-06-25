if (!verifyPath.includes(window.location.pathname)) {
    const logoutBtn = document.getElementById("logout")
    logoutBtn.addEventListener("click", function (event) {
        event.preventDefault()
        modal.style.display = "flex"
    
        const form = new FormData()
        form.append('request', JSON.stringify({ logout: true }))
    
        fetch(window.location.origin + "/admin/logout",
            {
                method: "POST",
                body: form
            }
        ).then((response) => response.json())
        .then(function (response) {
            
            if (response.error) {
                let message = response.error
                message = message.charAt(0).toUpperCase() + message.slice(1)
                toastr.error(message)
                throw new Error(message)
            }

            if (response.logout_success) {
                const url = window.location.origin + window.location.pathname
                window.location.href = url
            }
        })
    })
}
const endpointsElement = document.querySelector("[endpoints]")
if (endpointsElement) {
    let endpoints = JSON.parse(endpointsElement.getAttribute("endpoints"))

    if (endpoints.length > 0) {
        let sidebarMenu = Array.from(document.querySelector("[sidebarmenu]").firstElementChild.children)
        sidebarMenu = sidebarMenu.filter((element) => element.querySelectorAll(".nav-item"))
        sidebarMenu.forEach((element) => {
            let links = Array.from(element.querySelectorAll(".nav-link"))
            links = links.filter((link) => !link.href.match(/\w#$/g))

            if (links.length > 0) {
                links.forEach(function (linkElement) {
                    linkElement.addEventListener("click", function (event) {
                        event.preventDefault()
                        modal.style.display = "flex"
                        window.location.href = linkElement.href
                    })
                })
            }

            links = links.filter((link) => {
                const url = new URL(link.href)
                return endpoints.includes(url.pathname)
            })

            if (links.length > 0) {
                element.classList.add("menu-open")
                element.firstElementChild.classList.add("active")

                links.forEach((link) => {
                    link.closest("li").classList.add("menu-open")
                    link.classList.add("active")

                    link.closest("li").closest("ul").closest("li").classList.add("menu-open")
                    link.closest("li").closest("ul").closest("li").firstElementChild.classList.add("active")
                })
            }
        })
    }
}
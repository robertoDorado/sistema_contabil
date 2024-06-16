const endpointsElement = document.querySelector("[endpoints]")
if (endpointsElement) {
    let endpoints = JSON.parse(endpointsElement.getAttribute("endpoints"))
    
    if (endpoints.length > 0) {
        const sidebarMenu = document.querySelector("[sidebarMenu]")
        const menuAdmin = Array.from(sidebarMenu.firstElementChild.children)

        menuAdmin.forEach(function(element) {
            const navItem = element.querySelectorAll(".nav-item")
            
            navItem.forEach(function(element) {
                const navLink = element.firstElementChild
                const currentUrl = window.location.href.split("?").shift()
                
                if (navLink.href == currentUrl) {
                    navLink.classList.add("active")
                    const menuElement = element.closest("ul").closest("li")
                    if (menuElement) {
                        menuElement.classList.add("menu-open")
                        menuElement.firstElementChild.classList.add("active")
                    }

                    const submenuElement = menuElement.closest("ul").closest("li")
                    if (submenuElement) {
                        submenuElement.classList.add("menu-open")
                        submenuElement.firstElementChild.classList.add("active")
                    }
                }
            })
        })
    }
}
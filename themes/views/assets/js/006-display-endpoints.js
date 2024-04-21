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
                if (navLink.href == window.location.href) {
                    navLink.classList.add("active")
                    element.parentElement.parentElement.classList.add("menu-open")
                    element.parentElement.parentElement.firstElementChild.classList.add("active")
                }
            })
        })
    }
}
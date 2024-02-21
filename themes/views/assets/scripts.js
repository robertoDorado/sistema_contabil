if(window.location.pathname=="/admin/login"){const loginForm=document.getElementById("loginForm")
loginForm.addEventListener("submit",function(event){event.preventDefault()
const form=new FormData(this)
fetch(window.location.pathname,{method:"POST",body:form}).then(response=>response.json()).then(function(response){console.log(response)})})}
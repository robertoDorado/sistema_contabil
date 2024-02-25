function showSpinner(btn){const spinner=document.createElement("i")
spinner.classList.add("fas","fa-spinner","fa-spin")
btn.innerHTML=''
btn.appendChild(spinner)};if(window.location.pathname.match(/admin/)){window.addEventListener("load",function(){toastr.options={'closeButton':!0,'debug':!1,'newestOnTop':!1,'progressBar':!0,'positionClass':'toast-top-right','preventDuplicates':!1,'showDuration':'1000','hideDuration':'1000','timeOut':'5000','extendedTimeOut':'1000','showEasing':'swing','hideEasing':'linear','showMethod':'fadeIn','hideMethod':'fadeOut',}})};if(window.location.pathname=="/admin/login"){const loginForm=document.getElementById("loginForm")
loginForm.addEventListener("submit",function(event){event.preventDefault()
const btnSubmit=this.querySelector(".btn.btn-primary.btn-block")
if(this.userEmail.value==''){toastr.error("Campo e-mail deve ser obrigatório")
throw new Error("Campo e-mail deve ser obrigatório")}
if(!this.userEmail.value.match(/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/)){toastr.error("Este e-mail não é válido")
throw new Error("este e-mail não é válido")}
if(this.userPassword.value==''){toastr.error("Campo senha deve ser obrigatório")
throw new Error("campo senha deve ser obrigatório")}
if(this.csrfToken.value==''){toastr.error("Campo csrf-token inválido")
throw new Error("Campo csrf-token inválido")}
showSpinner(btnSubmit)
const form=new FormData(this)
fetch(window.location.pathname,{method:"POST",body:form}).then(response=>response.json()).then(function(response){let message=''
if(response.invalid_login_data){message=response.invalid_login_data
message=message.charAt(0).toUpperCase()+message.slice(1)
toastr.error(message)
btnSubmit.innerHTML='Login'
throw new Error(message)}
if(response.user_not_register){message=response.user_not_register
message=message.charAt(0).toUpperCase()+message.slice(1)
toastr.error(message)
btnSubmit.innerHTML='Login'
throw new Error(message)}
if(response.user_not_auth){message=response.user_not_auth
message=message.charAt(0).toUpperCase()+message.slice(1)
toastr.error(message)
btnSubmit.innerHTML='Login'
throw new Error(message)}
if(response.login_success){window.location.href=response.url}})})};if(window.location.pathname=="/admin"){const logoutBtn=document.getElementById("logout")
logoutBtn.addEventListener("click",function(event){event.preventDefault()
const form=new FormData()
form.append('request',JSON.stringify({logout:!0}))
fetch(window.location.origin+"/admin/logout",{method:"POST",body:form}).then((response)=>response.json()).then(function(response){if(response.logout_success){window.location.href=window.location.href}})})}
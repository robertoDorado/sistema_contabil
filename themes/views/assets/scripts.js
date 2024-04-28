function extensionFileName(value){return value.split(".").pop().toLowerCase()}
function dataTableConfig(jQuerySelector,objectConfigDataTable={}){return jQuerySelector.DataTable(objectConfigDataTable)}
function showSpinner(btn){const spinner=document.createElement("i")
spinner.classList.add("fas","fa-spinner","fa-spin")
btn.setAttribute("disabled","")
btn.innerHTML=''
btn.appendChild(spinner)};(function($){"use strict";if(!$.browser){$.browser={};$.browser.mozilla=/mozilla/.test(navigator.userAgent.toLowerCase())&&!/webkit/.test(navigator.userAgent.toLowerCase());$.browser.webkit=/webkit/.test(navigator.userAgent.toLowerCase());$.browser.opera=/opera/.test(navigator.userAgent.toLowerCase());$.browser.msie=/msie/.test(navigator.userAgent.toLowerCase());$.browser.device=/android|webos|iphone|ipad|ipod|blackberry|iemobile|opera mini/i.test(navigator.userAgent.toLowerCase())}
var defaultOptions={prefix:"",suffix:"",affixesStay:!0,thousands:",",decimal:".",precision:2,allowZero:!1,allowNegative:!1,doubleClickSelection:!0,allowEmpty:!1,bringCaretAtEndOnFocus:!0},methods={destroy:function(){$(this).unbind(".maskMoney");if($.browser.msie){this.onpaste=null}
return this},applyMask:function(value){var $input=$(this);var settings=$input.data("settings");return maskValue(value,settings)},mask:function(value){return this.each(function(){var $this=$(this);if(typeof value==="number"){$this.val(value)}
return $this.trigger("mask")})},unmasked:function(){return this.map(function(){var value=($(this).val()||"0"),isNegative=value.indexOf("-")!==-1,decimalPart;$(value.split(/\D/).reverse()).each(function(index,element){if(element){decimalPart=element;return!1}});value=value.replace(/\D/g,"");value=value.replace(new RegExp(decimalPart+"$"),"."+decimalPart);if(isNegative){value="-"+value}
return parseFloat(value)})},unmaskedWithOptions:function(){return this.map(function(){var value=($(this).val()||"0"),settings=$(this).data("settings")||defaultOptions,regExp=new RegExp((settings.thousandsForUnmasked||settings.thousands),"g");value=value.replace(regExp,"");return parseFloat(value)})},init:function(parameters){parameters=$.extend($.extend({},defaultOptions),parameters);return this.each(function(){var $input=$(this),settings,onFocusValue;settings=$.extend({},parameters);settings=$.extend(settings,$input.data());$input.data("settings",settings);function getInputSelection(){var el=$input.get(0),start=0,end=0,normalizedValue,range,textInputRange,len,endRange;if(typeof el.selectionStart==="number"&&typeof el.selectionEnd==="number"){start=el.selectionStart;end=el.selectionEnd}else{range=document.selection.createRange();if(range&&range.parentElement()===el){len=el.value.length;normalizedValue=el.value.replace(/\r\n/g,"\n");textInputRange=el.createTextRange();textInputRange.moveToBookmark(range.getBookmark());endRange=el.createTextRange();endRange.collapse(!1);if(textInputRange.compareEndPoints("StartToEnd",endRange)>-1){start=end=len}else{start=-textInputRange.moveStart("character",-len);start+=normalizedValue.slice(0,start).split("\n").length-1;if(textInputRange.compareEndPoints("EndToEnd",endRange)>-1){end=len}else{end=-textInputRange.moveEnd("character",-len);end+=normalizedValue.slice(0,end).split("\n").length-1}}}}
return{start:start,end:end}}
function canInputMoreNumbers(){var haventReachedMaxLength=!($input.val().length>=$input.attr("maxlength")&&$input.attr("maxlength")>=0),selection=getInputSelection(),start=selection.start,end=selection.end,haveNumberSelected=(selection.start!==selection.end&&$input.val().substring(start,end).match(/\d/))?!0:!1,startWithZero=($input.val().substring(0,1)==="0");return haventReachedMaxLength||haveNumberSelected||startWithZero}
function setCursorPosition(pos){if(!!settings.formatOnBlur){return}
$input.each(function(index,elem){if(elem.setSelectionRange){elem.focus();elem.setSelectionRange(pos,pos)}else if(elem.createTextRange){var range=elem.createTextRange();range.collapse(!0);range.moveEnd("character",pos);range.moveStart("character",pos);range.select()}})}
function maskAndPosition(startPos){var originalLen=$input.val().length,newLen;$input.val(maskValue($input.val(),settings));newLen=$input.val().length;if(!settings.reverse){startPos=startPos-(originalLen-newLen)}
setCursorPosition(startPos)}
function mask(){var value=$input.val();if(settings.allowEmpty&&value===""){return}
var isNumber=!isNaN(value);var decimalPointIndex=isNumber?value.indexOf("."):value.indexOf(settings.decimal);if(settings.precision>0){if(decimalPointIndex<0){value+=settings.decimal+new Array(settings.precision+1).join(0)}else{var integerPart=value.slice(0,decimalPointIndex),decimalPart=value.slice(decimalPointIndex+1);value=integerPart+settings.decimal+decimalPart+new Array((settings.precision+1)-decimalPart.length).join(0)}}else if(decimalPointIndex>0){value=value.slice(0,decimalPointIndex)}
$input.val(maskValue(value,settings))}
function changeSign(){var inputValue=$input.val();if(settings.allowNegative){if(inputValue!==""&&inputValue.charAt(0)==="-"){return inputValue.replace("-","")}else{return"-"+inputValue}}else{return inputValue}}
function preventDefault(e){if(e.preventDefault){e.preventDefault()}else{e.returnValue=!1}}
function fixMobile(){if($.browser.device){$input.attr("type","tel")}}
function keypressEvent(e){e=e||window.event;var key=e.which||e.charCode||e.keyCode,decimalKeyCode=settings.decimal.charCodeAt(0);if(key===undefined){return!1}
if((key<48||key>57)&&(key!==decimalKeyCode||!settings.reverse)){return handleAllKeysExceptNumericalDigits(key,e)}else if(!canInputMoreNumbers()){return!1}else{if(key===decimalKeyCode&&shouldPreventDecimalKey()){return!1}
if(settings.formatOnBlur){return!0}
preventDefault(e);applyMask(e);return!1}}
function shouldPreventDecimalKey(){if(isAllTextSelected()){return!1}
return alreadyContainsDecimal()}
function isAllTextSelected(){var length=$input.val().length;var selection=getInputSelection();return selection.start===0&&selection.end===length}
function alreadyContainsDecimal(){return $input.val().indexOf(settings.decimal)>-1}
function applyMask(e){e=e||window.event;var key=e.which||e.charCode||e.keyCode,keyPressedChar="",selection,startPos,endPos,value;if(key>=48&&key<=57){keyPressedChar=String.fromCharCode(key)}
selection=getInputSelection();startPos=selection.start;endPos=selection.end;value=$input.val();$input.val(value.substring(0,startPos)+keyPressedChar+value.substring(endPos,value.length));maskAndPosition(startPos+1)}
function handleAllKeysExceptNumericalDigits(key,e){if(key===45){$input.val(changeSign());return!1}else if(key===43){$input.val($input.val().replace("-",""));return!1}else if(key===13||key===9){return!0}else if($.browser.mozilla&&(key===37||key===39)&&e.charCode===0){return!0}else{preventDefault(e);return!0}}
function keydownEvent(e){e=e||window.event;var key=e.which||e.charCode||e.keyCode,selection,startPos,endPos,value,lastNumber;if(key===undefined){return!1}
selection=getInputSelection();startPos=selection.start;endPos=selection.end;if(key===8||key===46||key===63272){preventDefault(e);value=$input.val();if(startPos===endPos){if(key===8){if(settings.suffix===""){startPos-=1}else{lastNumber=value.split("").reverse().join("").search(/\d/);startPos=value.length-lastNumber-1;endPos=startPos+1}}else{endPos+=1}}
$input.val(value.substring(0,startPos)+value.substring(endPos,value.length));maskAndPosition(startPos);return!1}else if(key===9){return!0}else{return!0}}
function focusEvent(){onFocusValue=$input.val();mask();var input=$input.get(0),textRange;if(!!settings.selectAllOnFocus){input.select()}else if(input.createTextRange&&settings.bringCaretAtEndOnFocus){textRange=input.createTextRange();textRange.collapse(!1);textRange.select()}}
function cutPasteEvent(){setTimeout(function(){mask()},0)}
function getDefaultMask(){var n=parseFloat("0")/Math.pow(10,settings.precision);return(n.toFixed(settings.precision)).replace(new RegExp("\\.","g"),settings.decimal)}
function blurEvent(e){if($.browser.msie){keypressEvent(e)}
if(!!settings.formatOnBlur&&$input.val()!==onFocusValue){applyMask(e)}
if($input.val()===""&&settings.allowEmpty){$input.val("")}else if($input.val()===""||$input.val()===setSymbol(getDefaultMask(),settings)){if(!settings.allowZero){$input.val("")}else if(!settings.affixesStay){$input.val(getDefaultMask())}else{$input.val(setSymbol(getDefaultMask(),settings))}}else{if(!settings.affixesStay){var newValue=$input.val().replace(settings.prefix,"").replace(settings.suffix,"");$input.val(newValue)}}
if($input.val()!==onFocusValue){$input.change()}}
function clickEvent(){var input=$input.get(0),length;if(!!settings.selectAllOnFocus){return}else if(input.setSelectionRange&&settings.bringCaretAtEndOnFocus){length=$input.val().length;input.setSelectionRange(length,length)}else{$input.val($input.val())}}
function doubleClickEvent(){var input=$input.get(0),start,length;if(input.setSelectionRange&&settings.bringCaretAtEndOnFocus){length=$input.val().length;start=settings.doubleClickSelection?0:length;input.setSelectionRange(start,length)}else{$input.val($input.val())}}
fixMobile();$input.unbind(".maskMoney");$input.bind("keypress.maskMoney",keypressEvent);$input.bind("keydown.maskMoney",keydownEvent);$input.bind("blur.maskMoney",blurEvent);$input.bind("focus.maskMoney",focusEvent);$input.bind("click.maskMoney",clickEvent);$input.bind("dblclick.maskMoney",doubleClickEvent);$input.bind("cut.maskMoney",cutPasteEvent);$input.bind("paste.maskMoney",cutPasteEvent);$input.bind("mask.maskMoney",mask)})}};function setSymbol(value,settings){var operator="";if(value.indexOf("-")>-1){value=value.replace("-","");operator="-"}
if(value.indexOf(settings.prefix)>-1){value=value.replace(settings.prefix,"")}
if(value.indexOf(settings.suffix)>-1){value=value.replace(settings.suffix,"")}
return operator+settings.prefix+value+settings.suffix}
function maskValue(value,settings){if(settings.allowEmpty&&value===""){return""}
if(!!settings.reverse){return maskValueReverse(value,settings)}
return maskValueStandard(value,settings)}
function maskValueStandard(value,settings){var negative=(value.indexOf("-")>-1&&settings.allowNegative)?"-":"",onlyNumbers=value.replace(/[^0-9]/g,""),integerPart=onlyNumbers.slice(0,onlyNumbers.length-settings.precision),newValue,decimalPart,leadingZeros;newValue=buildIntegerPart(integerPart,negative,settings);if(settings.precision>0){if(!isNaN(value)&&value.indexOf(".")){var precision=value.substr(value.indexOf(".")+1);onlyNumbers+=new Array((settings.precision+1)-precision.length).join(0);integerPart=onlyNumbers.slice(0,onlyNumbers.length-settings.precision);newValue=buildIntegerPart(integerPart,negative,settings)}
decimalPart=onlyNumbers.slice(onlyNumbers.length-settings.precision);leadingZeros=new Array((settings.precision+1)-decimalPart.length).join(0);newValue+=settings.decimal+leadingZeros+decimalPart}
return setSymbol(newValue,settings)}
function maskValueReverse(value,settings){var negative=(value.indexOf("-")>-1&&settings.allowNegative)?"-":"",valueWithoutSymbol=value.replace(settings.prefix,"").replace(settings.suffix,""),integerPart=valueWithoutSymbol.split(settings.decimal)[0],newValue,decimalPart="";if(integerPart===""){integerPart="0"}
newValue=buildIntegerPart(integerPart,negative,settings);if(settings.precision>0){var arr=valueWithoutSymbol.split(settings.decimal);if(arr.length>1){decimalPart=arr[1]}
newValue+=settings.decimal+decimalPart;var rounded=Number.parseFloat((integerPart+"."+decimalPart)).toFixed(settings.precision);var roundedDecimalPart=rounded.toString().split(settings.decimal)[1];newValue=newValue.split(settings.decimal)[0]+"."+roundedDecimalPart}
return setSymbol(newValue,settings)}
function buildIntegerPart(integerPart,negative,settings){integerPart=integerPart.replace(/^0*/g,"");integerPart=integerPart.replace(/\B(?=(\d{3})+(?!\d))/g,settings.thousands);if(integerPart===""){integerPart="0"}
return negative+integerPart}
$.fn.maskMoney=function(method){if(methods[method]){return methods[method].apply(this,Array.prototype.slice.call(arguments,1))}else if(typeof method==="object"||!method){return methods.init.apply(this,arguments)}else{$.error("Method "+method+" does not exist on jQuery.maskMoney")}}})(window.jQuery||window.Zepto);const urlJson=document.getElementById("urlJson").dataset.url
const cashFlowTable=dataTableConfig($("#cashFlowReport"),{"order":[[0,"desc"]],"language":{"url":urlJson},"responsive":!0,"lengthChange":!1,"autoWidth":!1,"buttons":["copy",{extend:"csv",charset:'utf-8',bom:!0,customize:function(csvData){let arrayCsvData=csvData.split('"')
arrayCsvData=arrayCsvData.map(function(item){item=item.replace(/^Editar$/,"")
item=item.replace(/^Excluir$/,"")
return item})
arrayCsvData=arrayCsvData.filter((string)=>string)
for(let i=arrayCsvData.length-1;i>0;i--){if(arrayCsvData[i]==arrayCsvData[i-1]){arrayCsvData.splice(i-1,2)}}
let templateCsv=arrayCsvData.join('"')
if(!templateCsv.startsWith('"')){templateCsv=`"${templateCsv}`}
if(!templateCsv.endsWith('"')){templateCsv=`${templateCsv}"`}
return templateCsv}},{extend:"excel",customizeData:function(xlsxData){let balance=0
let arrayXlsxData=Array.from(xlsxData.body)
arrayXlsxData=arrayXlsxData.map(function(row){row[5]=parseFloat(row[5].replace("R$","").replace(".","").replace(",",".").trim())
row=row.filter((data)=>data)
balance+=row[5]
return row})
arrayXlsxData.push(['Total','','','','',balance])
xlsxData.header=xlsxData.header.filter((data)=>data!='Editar'&&data!='Excluir')
xlsxData.body=arrayXlsxData}},{extend:"pdf",customize:function(pdfData){let arrayPdfData=Array.from(pdfData.content[1].table.body)
let header=arrayPdfData.shift()
let balance=0
arrayPdfData=arrayPdfData.map(function(row){row[5].text=parseFloat(row[5].text.replace("R$","").replace(".","").replace(",",".").trim())
balance+=row[5].text
row[5].text=row[5].text.toLocaleString("pt-br",{"currency":"BRL","style":"currency"})
return row.filter((data)=>data.text)})
balance=balance.toLocaleString("pt-br",{"currency":"BRL","style":"currency"})
header=header.filter((item)=>item.text!="Editar"&&item.text!="Excluir")
arrayPdfData.unshift(header)
arrayPdfData.push([{text:'Total',style:'tableBodyOdd',fillColor:'#f1ff32'},{text:'',style:'tableBodyOdd',fillColor:'#f1ff32'},{text:'',style:'tableBodyOdd',fillColor:'#f1ff32'},{text:'',style:'tableBodyOdd',fillColor:'#f1ff32'},{text:'',style:'tableBodyOdd',fillColor:'#f1ff32'},{text:balance,style:'tableBodyOdd',fillColor:'#f1ff32'}])
pdfData.content[1].table.body=arrayPdfData}},"colvis"],"initComplete":function(){this.api().buttons().container().appendTo("#widgets .col-md-6:eq(0)")}})
const cashFlowGroupTable=dataTableConfig($("#cashFlowGroupReport"),{"language":{"url":urlJson}})
const cashFlowGroupDeletedReport=dataTableConfig($("#cashFlowGroupDeletedReport"),{"language":{"url":urlJson}})
const cashFlowDeletedReport=dataTableConfig($("#cashFlowDeletedReport"),{"language":{"url":urlJson}});if(window.location.pathname.match(/admin/)){window.addEventListener("load",function(){toastr.options={'closeButton':!0,'debug':!1,'newestOnTop':!1,'progressBar':!0,'positionClass':'toast-top-right','preventDuplicates':!1,'showDuration':'1000','hideDuration':'1000','timeOut':'5000','extendedTimeOut':'1000','showEasing':'swing','hideEasing':'linear','showMethod':'fadeIn','hideMethod':'fadeOut',}})};const verifyPath=['/admin/login','/customer/subscribe','/customer/subscription/thanks-purchase'];const endpointsElement=document.querySelector("[endpoints]")
if(endpointsElement){let endpoints=JSON.parse(endpointsElement.getAttribute("endpoints"))
if(endpoints.length>0){const sidebarMenu=document.querySelector("[sidebarMenu]")
const menuAdmin=Array.from(sidebarMenu.firstElementChild.children)
menuAdmin.forEach(function(element){const navItem=element.querySelectorAll(".nav-item")
navItem.forEach(function(element){const navLink=element.firstElementChild
if(navLink.href==window.location.href){navLink.classList.add("active")
element.parentElement.parentElement.classList.add("menu-open")
element.parentElement.parentElement.firstElementChild.classList.add("active")}})})}};if(window.location.pathname=="/admin/customer/cancel-subscription"){const cancelSubscription=document.querySelector("[cancelSubscription]")
const saveChanges=document.getElementById("saveChanges")
const dismissModal=document.getElementById("dismissModal")
let cancelBtnTitle="Cancelar a minha assinatura"
cancelSubscription.addEventListener("click",function(){const launchModal=document.getElementById("launchModal")
launchModal.click()
const modalContainerLabel=document.getElementById("modalContainerLabel")
const modalBody=document.querySelector(".modal-body")
saveChanges.classList.remove("btn-primary")
saveChanges.classList.add("btn-danger")
modalContainerLabel.innerHTML="Cancelamento da assinatura"
modalBody.innerHTML="Deseja mesmo cancelar a sua assinatura permanentemente?"
dismissModal.innerHTML="Não cancelar"
saveChanges.innerHTML=cancelBtnTitle})
saveChanges.addEventListener("click",function(){const btnCancelSubscription=this
showSpinner(btnCancelSubscription)
const form=new FormData()
form.append("cancelData",!0)
fetch(window.location.origin+"/customer/subscription/cancel-subscription",{method:"POST",body:form}).then(response=>response.json()).then(function(response){let message=""
if(response.error){dismissModal.click()
btnCancelSubscription.removeAttribute("disabled")
btnCancelSubscription.innerHTML=cancelBtnTitle
message=response.error
message=message.charAt(0).toUpperCase()+message.slice(1)
toastr.error(message)
throw new Error(message)}
if(response.success){dismissModal.click()
btnCancelSubscription.removeAttribute("disabled")
btnCancelSubscription.innerHTML=cancelBtnTitle
message=response.success
message=message.charAt(0).toUpperCase()+message.slice(1)
toastr.success(message)
setTimeout(()=>{window.location.href=response.url},3000)}})})};if(window.location.pathname=="/admin/cash-flow/backup/report"){const cashFlowDeletedTableReport=document.getElementById("cashFlowDeletedReport")
const launchModal=document.getElementById("launchModal")
const modalContainer=document.getElementById("modalContainer")
const saveChanges=modalContainer.querySelector("#saveChanges")
const dismissModal=modalContainer.querySelector("#dismissModal")
const tBody=Array.from(cashFlowDeletedTableReport.querySelector("tBody").children)
const data={restore:!1,destroy:!1}
tBody.forEach(function(row){const btnRestoreData=row.lastElementChild.previousElementSibling.firstElementChild
const btnDestroyData=row.lastElementChild.firstElementChild
btnRestoreData.addEventListener("click",function(event){event.preventDefault()
const uuid=this.parentElement.previousElementSibling.previousElementSibling.previousElementSibling.previousElementSibling.previousElementSibling.previousElementSibling.innerHTML
const row=this.parentElement.parentElement
data.row=row
data.uuid=uuid
data.restore=!0
data.destroy=!1
launchModal.click()})
btnDestroyData.addEventListener("click",function(event){event.preventDefault()
const uuid=this.parentElement.previousElementSibling.previousElementSibling.previousElementSibling.previousElementSibling.previousElementSibling.previousElementSibling.previousElementSibling.innerHTML
const row=this.parentElement.parentElement
data.row=row
data.uuid=uuid
data.destroy=!0
data.restore=!1
launchModal.click()})})
launchModal.addEventListener("click",function(){if(data.restore){saveChanges.innerHTML="Restaurar"
saveChanges.classList.remove("btn-danger")
saveChanges.classList.add("btn-primary")
dismissModal.innerHTML="Voltar";modalContainer.querySelector("#modalContainerLabel").innerHTML="Restaurar registro"
modalContainer.querySelector(".modal-body").innerHTML=`Deseja mesmo restaurar o registro ${data.uuid}?`}
if(data.destroy){saveChanges.innerHTML="Excluir"
saveChanges.classList.remove("btn-primary")
saveChanges.classList.add("btn-danger")
dismissModal.innerHTML="Voltar";modalContainer.querySelector("#modalContainerLabel").innerHTML="Excluir registro"
modalContainer.querySelector(".modal-body").innerHTML=`Deseja mesmo excluir permanentemente o registro ${data.uuid}?`}})
saveChanges.addEventListener("click",function(){const saveChanges=this
showSpinner(saveChanges)
const form=new FormData()
form.append("destroy",data.destroy)
form.append("restore",data.restore)
fetch(window.location.origin+`/admin/cash-flow/modify/${data.uuid}`,{method:"POST",body:form}).then(response=>response.json()).then(function(response){let message=""
saveChanges.innerHTML="Restaurar"
saveChanges.removeAttribute("disabled")
if(response.error){message=response.error
message=message.charAt(0).toUpperCase()+message.slice(1)
toastr.error(message)
throw new Error(message)}
if(response.success){message=response.success
message=message.charAt(0).toUpperCase()+message.slice(1)
toastr.success(message)
cashFlowDeletedReport.row(data.row).remove().draw()
dismissModal.click()}})})};if(window.location.pathname=='/admin/cash-flow/form'){const cashFlowForm=document.getElementById("cashFlowForm")
$("#launchValue").maskMoney({allowNegative:!1,thousands:'.',decimal:',',affixesStay:!1})
const launchBtn=document.getElementById("launchBtn")
cashFlowForm.addEventListener("submit",function(event){event.preventDefault()
if(!this.launchValue.value){toastr.warning("Campo valor de entrada inválido")
throw new Error('Campo valor de entrada é obrigatório')}
if(!this.csrfToken.value){toastr.warning("Campo csrf-token inválido")
throw new Error("Campo csrf-token inválido")}
if(!this.releaseHistory.value){toastr.warning("Campo histórico inválido")
throw new Error("Campo histórico inválido")}
if(!this.entryType.value){toastr.warning("Campo tipo de entrada inválido")
throw new Error("Campo tipo de entrada inválido")}
if(!this.accountGroup.value){toastr.warning("Campo grupo de contas inválido")
throw new Error("Campo grupo de contas inválido")}
const cashFlowFormFields=[this.launchValue,this.releaseHistory,this.entryType,this.accountGroup]
showSpinner(launchBtn)
const form=new FormData(this)
fetch(window.location.href,{method:"POST",body:form}).then((response)=>response.json()).then(function(response){let message=''
launchBtn.innerHTML='Enviar'
launchBtn.removeAttribute("disabled")
if(response.user_not_exists){message=response.user_not_exists
message=message.charAt(0).toUpperCase()+message.slice(1)
toastr.error(message)
throw new Error(message)}
if(response.invalid_entry_type){message=response.invalid_entry_type
message=message.charAt(0).toUpperCase()+message.slice(1)
toastr.error(message)
throw new Error(message)}
if(response.invalid_persist_data){message=response.invalid_persist_data
message=message.charAt(0).toUpperCase()+message.slice(1)
toastr.error(message)
throw new Error(message)}
if(response.error){message=response.error
message=message.charAt(0).toUpperCase()+message.slice(1)
toastr.error(message)
throw new Error(message)}
cashFlowFormFields.forEach(function(elem){elem.value=''})
message=response.success
message=message.charAt(0).toUpperCase()+message.slice(1)
toastr.success(message)})})};if(window.location.pathname=="/admin/cash-flow-group/backup/report"){const cashFlowGroupDeletedTableReport=document.getElementById("cashFlowGroupDeletedReport")
const launchModal=document.getElementById("launchModal")
const modalContainer=document.getElementById("modalContainer")
const saveChanges=modalContainer.querySelector("#saveChanges")
const dismissModal=modalContainer.querySelector("#dismissModal")
const tBody=Array.from(cashFlowGroupDeletedTableReport.querySelector("tBody").children)
const data={restore:!1,destroy:!1}
tBody.forEach(function(row){const btnRestoreData=row.lastElementChild.previousElementSibling.firstElementChild
const btnDestroyData=row.lastElementChild.firstElementChild
btnRestoreData.addEventListener("click",function(event){event.preventDefault()
const uuid=this.parentElement.previousElementSibling.previousElementSibling.innerHTML
const row=this.parentElement.parentElement
data.row=row
data.uuid=uuid
data.restore=!0
data.destroy=!1
launchModal.click()})
btnDestroyData.addEventListener("click",function(event){event.preventDefault()
const uuid=this.parentElement.previousElementSibling.previousElementSibling.previousElementSibling.innerHTML
const row=this.parentElement.parentElement
data.row=row
data.uuid=uuid
data.destroy=!0
data.restore=!1
launchModal.click()})})
launchModal.addEventListener("click",function(){if(data.restore){saveChanges.innerHTML="Restaurar"
saveChanges.classList.remove("btn-danger")
saveChanges.classList.add("btn-primary")
dismissModal.innerHTML="Voltar";modalContainer.querySelector("#modalContainerLabel").innerHTML="Restaurar registro"
modalContainer.querySelector(".modal-body").innerHTML=`Deseja mesmo restaurar o registro ${data.uuid}?`}
if(data.destroy){saveChanges.innerHTML="Excluir"
saveChanges.classList.remove("btn-primary")
saveChanges.classList.add("btn-danger")
dismissModal.innerHTML="Voltar";modalContainer.querySelector("#modalContainerLabel").innerHTML="Excluir registro"
modalContainer.querySelector(".modal-body").innerHTML=`Deseja mesmo excluir permanentemente o registro ${data.uuid}?`}})
saveChanges.addEventListener("click",function(){const saveChanges=this
showSpinner(saveChanges)
const form=new FormData()
form.append("destroy",data.destroy)
form.append("restore",data.restore)
fetch(window.location.origin+`/admin/cash-flow-group/modify/${data.uuid}`,{method:"POST",body:form}).then(response=>response.json()).then(function(response){let message=""
saveChanges.innerHTML="Restaurar"
saveChanges.removeAttribute("disabled")
if(response.error){message=response.error
message=message.charAt(0).toUpperCase()+message.slice(1)
toastr.error(message)
throw new Error(message)}
if(response.success){message=response.success
message=message.charAt(0).toUpperCase()+message.slice(1)
toastr.success(message)
cashFlowGroupDeletedReport.row(data.row).remove().draw()
dismissModal.click()}})})};if(window.location.pathname=="/admin/cash-flow-group/form"){const cashFlowGroupForm=document.getElementById("cashFlowGroupForm")
cashFlowGroupForm.addEventListener("submit",function(event){event.preventDefault()
const btnSubmit=this.querySelector("[type='submit']")
let accountGroup=this.accountGroup
if(!this.csrfToken.value){toastr.warning("Campo token não pode estar vazio")
throw new Error("Campo token não pode estar vazio")}
if(!accountGroup.value){toastr.warning("Campo nome do grupo não pode estar vazio")
throw new Error("Campo nome do grupo não pode estar vazio")}
showSpinner(btnSubmit)
const form=new FormData(this)
fetch(window.location.origin+"/admin/cash-flow-group/form",{method:"POST",body:form}).then((response)=>response.json()).then(function(response){let message=""
btnSubmit.innerHTML="Enviar"
btnSubmit.removeAttribute("disabled")
if(response.error){message=response.error.charAt(0).toUpperCase()+response.error.slice(1)
toastr.error(message)
throw new Error(message)}
accountGroup.value=""
message=response.success.charAt(0).toUpperCase()+response.success.slice(1)
toastr.success(message)})})};if(window.location.pathname=="/admin/cash-flow/report"){fetch(window.location.origin+"/admin/cash-flow/chart-line-data"+window.location.search).then(response=>response.json()).then(function(response){const containerChartLine=document.getElementById("containerChartLine")
if(response.created_at&&response.entry){containerChartLine.style.display="block"
const financeData={labels:response.created_at,datasets:[{label:"Projeção financeira",data:response.entry,borderColor:'rgb(75, 192, 192)',borderWidth:1,fill:!1}]};const chartOptions={scales:{y:{title:{display:!0,text:'Valor Financeiro'},ticks:{callback:function(value){return'R$ '+value.toLocaleString('pt-BR')}}},x:{title:{display:!0,text:'Tempo (Dias)'}}}};const ctx=document.getElementById('lineChartCashFlowReport').getContext('2d');new Chart(ctx,{type:'line',data:financeData,options:chartOptions})}})};if(window.location.pathname=="/admin/cash-flow/report"){fetch(window.location.origin+"/admin/cash-flow/chart-pie-data").then(response=>response.json()).then(function(response){const containerPieChart=document.getElementById("containerPieChart")
if(response.total_accounts&&response.accounts_data){containerPieChart.style.display="block"
const data={labels:response.accounts_data,datasets:[{label:'Grupo de contas',data:response.total_accounts,backgroundColor:['rgba(255, 99, 132, 0.5)','rgba(54, 162, 235, 0.5)','rgba(255, 206, 86, 0.5)','rgba(75, 192, 192, 0.5)','rgba(153, 102, 255, 0.5)'],borderColor:['rgba(255, 99, 132, 1)','rgba(54, 162, 235, 1)','rgba(255, 206, 86, 1)','rgba(75, 192, 192, 1)','rgba(153, 102, 255, 1)'],borderWidth:1}]};const config={type:'pie',data:data,options:{responsive:!0,title:{display:!0,text:'Gráfico de Pizza - Despesas Mensais'}}};new Chart(document.getElementById('pieChartCashFlowReport'),config)}})};if(window.location.pathname=="/admin/cash-flow/report"){$(document).ready(function(){$('#date-range').daterangepicker({opens:'left',locale:{format:'DD/MM/YYYY',separator:' - ',applyLabel:'Aplicar',cancelLabel:'Cancelar',}})});const tFoot=document.querySelector("tfoot").firstElementChild
cashFlowTable.on('search.dt',function(){const dataFilter=cashFlowTable.rows({search:'applied'}).data();let balance=0
dataFilter.each(function(row){let entryValue=parseFloat(row[5].replace("R$","").replace(".","").replace(",",".").trim())
balance+=entryValue})
balance<0?tFoot.style.color="#ff0000":balance==0?tFoot.removeAttribute("style"):tFoot.style.color="#008000"
tFoot.children[5].innerHTML=balance.toLocaleString("pt-br",{"currency":"BRL","style":"currency"})})};if(window.location.pathname=="/customer/subscribe"){$(document).ready(function(){$("[name='birthDate']").datepicker({format:"dd/mm/yyyy",language:"pt-BR",autoclose:!0})});const verifyDocument={"14":function(value){return value.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/,"$1.$2.$3/$4-$5")},"11":function(value){return value.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})$/,"$1.$2.$3-$4")}}
const documentData=document.querySelector("[name='document']")
documentData.addEventListener("input",function(){this.value=this.value.replace(/\D/g,"")
if(typeof verifyDocument[this.value.length]=="function"){this.value=verifyDocument[this.value.length](this.value)}
if(this.value.length>=14){this.maxLength=18}})
const birthDate=document.querySelector("[name='birthDate']")
birthDate.addEventListener("input",function(){this.value=this.value.replace(/\D/g,"").replace(/(\d{2})(\d)/,"$1/$2").replace(/(\d{2})(\d)/,"$1/$2").replace(/(\/\d{4})\d+?$/,"$1")})
const zipcode=document.querySelector("[name='zipcode']")
zipcode.addEventListener("input",function(){this.value=this.value.replace(/\D/g,"").replace(/(\d{5})(\d)/,"$1-$2").replace(/(-\d{3})\d+?$/,"$1")
const searchValue=this.value.replace(/[^\d]+/,"")
if(searchValue.length>=8){fetch(`https://brasilapi.com.br/api/cep/v1/${searchValue}`).then(response=>response.json()).then(function(response){if(response.cep){document.querySelector('[name="address"]').value=response.street
document.querySelector('[name="neighborhood"]').value=response.neighborhood
document.querySelector('[name="city"]').value=response.city
document.querySelector('[name="state"]').value=response.state}})}})
const addressNumber=document.querySelector("[name='number']")
addressNumber.addEventListener("input",function(){this.value=this.value.replace(/\D/g,"")})
const state=document.querySelector("[name='state']")
state.addEventListener("input",function(){this.value=this.value.replace(/[^A-Za-z]+/g,'').toUpperCase().replace(/([A-Z]{2})[A-Z]+?$/,"$1")})
const phone=document.querySelector("[name='phone']")
phone.addEventListener("input",function(){this.value=this.value.replace(/\D/g,"").replace(/(\d{2})(\d)/,"($1) $2").replace(/(\d{4})(\d)/,"$1-$2").replace(/(-\d{4})\d+?$/,"$1")})
const cellPhone=document.querySelector("[name='cellPhone']")
cellPhone.addEventListener("input",function(){this.value=this.value.replace(/\D/g,"").replace(/(\d{2})(\d)/,"($1) $2").replace(/(\d{5})(\d)/,"$1-$2").replace(/(-\d{4})\d+?$/,"$1")})
const stripe=Stripe("pk_test_51OEIojC1Uv10wqUudCsaCYGleVine1HcYMo3kLbOJDbFnetTHFMLkCEiCt24J256ahte6UCvHkBfFMrlEIT7qFlE00LQx8SDKD",{locale:"pt-BR"})
const elements=stripe.elements()
const style={base:{fontSize:'16px',fontFamily:'"Helvetica Neue", Helvetica, sans-serif',fontSmoothing:'antialiased',color:'#555','::placeholder':{color:'#999'}},invalid:{color:'#fa755a',iconColor:'#fa755a'}}
const card=elements.create('card',{style:style})
const cardMount=document.getElementById("cardMount")
card.mount(cardMount)
const subscriptionForm=document.getElementById("subscriptionForm")
subscriptionForm.addEventListener("submit",function(event){event.preventDefault()
const btnSubmit=this.querySelector("button[type='submit']")
if(/\s/.test(this.userName.value)){toastr.warning("Nome de usuário não pode conter espaços em branco")
throw new Error("Nome de usuário não pode conter espaços em branco")}
let validateBlankInput=Array.from(this.getElementsByTagName("input"))
validateBlankInput=validateBlankInput.filter(function(element){if(!element.classList.contains("__PrivateStripeElement-input")&&element.name!="phone"&&element.name!="cellPhone"){return element}})
validateBlankInput.forEach(function(element){if(!element.value){toastr.warning(`Campos obrigatórios não foram preenchidos`)
throw new Error(`Campos obrigatórios não foram preenchidos`)}})
const selectField=this.querySelector("select")
if(!selectField.value){toastr.warning(`Campos obrigatórios não foram preenchidos`)
throw new Error(`Campos obrigatórios não foram preenchidos`)}
if(this.password.value!=this.confirmPassword.value){toastr.warning("As senhas não conferem")
throw new Error("As senhas não conferem")}
const form=new FormData(this)
showSpinner(btnSubmit)
stripe.createToken(card).then(function(response){let message=""
if(response.error){btnSubmit.innerHTML="Comprar assinatura"
btnSubmit.removeAttribute("disabled")
message=response.error.message
message=message.charAt(0).toUpperCase()+message.slice(1)
toastr.error(message)
throw new Error(message)}
form.append("cardToken",response.token.id)
fetch(window.location.origin+"/customer/subscription/process-payment",{method:"POST",body:form}).then(response=>response.json()).then(function(response){if(response.error){btnSubmit.innerHTML="Comprar assinatura"
btnSubmit.removeAttribute("disabled")
message=response.error
message=message.charAt(0).toUpperCase()+message.slice(1)
toastr.error(message)
throw new Error(message)}
if(response.success){window.location.href=response.url}})})})};if(window.location.pathname=="/admin/customer/update-data/form"){$(document).ready(function(){$("[name='birthDate']").datepicker({format:"dd/mm/yyyy",language:"pt-BR",autoclose:!0})});const maskDocument={"11":function(value){return value.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})$/,"$1.$2.$3-$4")},"14":function(value){return value.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/,"$1.$2.$3/$4-$5")}}
const customerDocument=document.querySelector("[name='document']")
customerDocument.addEventListener("input",function(){this.value=this.value.replace(/\D/g,"")
if(typeof maskDocument[this.value.length]=="function"){this.value=maskDocument[this.value.length](this.value)}
if(this.value.length>=14){this.maxLength=18}})
const birthDate=document.querySelector("[name='birthDate']")
birthDate.addEventListener("input",function(){this.value=this.value.replace(/\D/g,"").replace(/(\d{2})(\d)/,"$1/$2").replace(/(\d{2})(\d)/,"$1/$2").replace(/(\/\d{4})\d+?$/,"$1")})
const zipcode=document.querySelector("[name='zipcode']")
zipcode.addEventListener("input",function(){this.value=this.value.replace(/\D/g,"").replace(/(\d{5})(\d)/,"$1-$2").replace(/(-\d{3})\d+?$/,"$1")
const searchValue=this.value.replace(/[^\d]+/,"")
if(searchValue.length>=8){fetch(`https://brasilapi.com.br/api/cep/v1/${searchValue}`).then(response=>response.json()).then(function(response){if(response.cep){document.querySelector('[name="address"]').value=response.street
document.querySelector('[name="neighborhood"]').value=response.neighborhood
document.querySelector('[name="city"]').value=response.city
document.querySelector('[name="state"]').value=response.state}})}})
const addressNumber=document.querySelector("[name='number']")
addressNumber.addEventListener("input",function(){this.value=this.value.replace(/\D/g,"")})
const state=document.querySelector("[name='state']")
state.addEventListener("input",function(){this.value=this.value.replace(/[^A-Za-z]+/g,'').toUpperCase().replace(/([A-Z]{2})[A-Z]+?$/,"$1")})
const phone=document.querySelector("[name='phone']")
phone.addEventListener("input",function(){this.value=this.value.replace(/\D/g,"").replace(/(\d{2})(\d)/,"($1) $2").replace(/(\d{4})(\d)/,"$1-$2").replace(/(-\d{4})\d+?$/,"$1")})
const cellPhone=document.querySelector("[name='cellPhone']")
cellPhone.addEventListener("input",function(){this.value=this.value.replace(/\D/g,"").replace(/(\d{2})(\d)/,"($1) $2").replace(/(\d{5})(\d)/,"$1-$2").replace(/(-\d{4})\d+?$/,"$1")})
const customerUpdateForm=document.getElementById("customerUpdateForm")
customerUpdateForm.addEventListener("submit",function(event){event.preventDefault()
const btnSubmit=this.querySelector("[type='submit']")
let message=""
if(/\s/.test(this.userName.value)){message="Nome de usuário não pode conter espaços em branco"
toastr.warning(message)
throw new Error(message)}
if(this.password.value!=this.confirmPassword.value){message="As senhas não conferem"
toastr.error(message)
throw new Error(message)}
let validateBlankInput=Array.from(this.getElementsByTagName("input"))
validateBlankInput=validateBlankInput.filter(function(element){if(element.name!="phone"&&element.name!="cellPhone"){return element}})
validateBlankInput.forEach(function(element){if(!element.value){message="Campos obrigatórios não foram preenchidos"
toastr.warning(message)
throw new Error(message)}})
showSpinner(btnSubmit)
const form=new FormData(this)
fetch(window.location.href,{method:"POST",body:form}).then(response=>response.json()).then(function(response){if(response.error){btnSubmit.removeAttribute("disabled")
btnSubmit.innerHTML="Atualizar"
message=response.error
message=message.charAt(0).toUpperCase()+message.slice(1)
toastr.error(message)
throw new Error(message)}
if(response.success){let fullName=response.fullName
fullName=fullName.split(" ")
fullName=fullName.map((name)=>name.charAt(0).toUpperCase()+name.slice(1))
fullName=fullName.slice(0,3).join(" ")
const userPanel=document.querySelector(".user-panel")
userPanel.firstElementChild.innerHTML=`Bem vindo ${fullName}`
btnSubmit.removeAttribute("disabled")
btnSubmit.innerHTML="Atualizar"
message=response.success
message=message.charAt(0).toUpperCase()+message.slice(1)
toastr.success(message)}})})};if(window.location.pathname=='/admin/cash-flow/report'){const importExcelForm=document.getElementById("importExcelForm")
const inputExcelFile=document.querySelector('[name="excelFile"]')
const standardLabelNameExcelFile=inputExcelFile.nextElementSibling.innerHTML
const verifyExtensionFile=["xls","xlsx"]
inputExcelFile.addEventListener("change",function(){const extensionName=extensionFileName(this.value)
if(verifyExtensionFile.indexOf(extensionName)==-1){this.value=""
this.nextElementSibling.innerHTML=standardLabelNameExcelFile
toastr.warning("Extensão do arquivo não permitida")
throw new Error("Extensão do arquivo não permitida")}
this.nextElementSibling.innerHTML=this.files[0].name})
importExcelForm.addEventListener("submit",function(event){event.preventDefault()
const extensionName=extensionFileName(this.excelFile.value)
const btnSubmit=this.querySelector('[type="submit"]')
const importIcon=btnSubmit.firstElementChild
const excelFile=this.excelFile
const excelLabel=this.excelFile.nextElementSibling
if(verifyExtensionFile.indexOf(extensionName)==-1){excelFile.value=""
excelLabel.innerHTML=standardLabelNameExcelFile
toastr.warning("Extensão do arquivo não permitida")
throw new Error("Extensão do arquivo não permitida")}
showSpinner(btnSubmit)
const spinner=btnSubmit.querySelector(".fas.fa-spinner.fa-spin")
const form=new FormData(this)
fetch(window.location.origin+"/admin/cash-flow/import-excel",{method:'POST',body:form}).then(response=>response.json()).then(function(response){spinner.remove()
btnSubmit.append(importIcon," Importar ")
btnSubmit.removeAttribute("disabled")
let message=""
if(response.error){excelFile.value=""
excelLabel.innerHTML=standardLabelNameExcelFile
message=response.error.charAt(0).toUpperCase()+response.error.slice(1)
toastr.error(message)}
if(response.success||response.full_success){excelFile.value=""
excelLabel.innerHTML=standardLabelNameExcelFile
const excelData=JSON.parse(response.excelData)
for(let i=0;i<excelData.Histórico.length;i++){cashFlowTable.row.add([excelData.Id[i],excelData["Grupo de contas"][i],excelData["Data lançamento"][i],excelData.Histórico[i],excelData["Tipo de entrada"][i],excelData.Lançamento[i],excelData.Editar[i],excelData.Excluir[i]]).draw(!1)}
message=response.full_success
message=message.charAt(0).toUpperCase()+message.slice(1)
toastr.success(message)}})})};if(window.location.pathname=="/admin/login"){const loginForm=document.getElementById("loginForm")
loginForm.addEventListener("submit",function(event){event.preventDefault()
const btnSubmit=this.querySelector(".btn.btn-primary.btn-block")
if(!this.userData.value){toastr.warning("Campo nome de usuário deve ser obrigatório")
throw new Error("Campo nome de usuário deve ser obrigatório")}
if(!this.userPassword.value){toastr.warning("Campo senha deve ser obrigatório")
throw new Error("campo senha deve ser obrigatório")}
if(!this.csrfToken.value){toastr.warning("Campo csrf-token inválido")
throw new Error("Campo csrf-token inválido")}
showSpinner(btnSubmit)
const form=new FormData(this)
fetch(window.location.pathname,{method:"POST",body:form}).then(response=>response.json()).then(function(response){let message=''
if(response.error){message=response.error
message=message.charAt(0).toUpperCase()+message.slice(1)
toastr.error(message)
btnSubmit.innerHTML='Login'
btnSubmit.removeAttribute("disabled")
throw new Error(message)}
if(response.login_success){window.location.href=response.url}})})};if(!verifyPath.includes(window.location.pathname)){const logoutBtn=document.getElementById("logout")
logoutBtn.addEventListener("click",function(event){event.preventDefault()
const form=new FormData()
form.append('request',JSON.stringify({logout:!0}))
fetch(window.location.origin+"/admin/logout",{method:"POST",body:form}).then((response)=>response.json()).then(function(response){if(response.error){let message=response.error
message=message.charAt(0).toUpperCase()+message.slice(1)
toastr.error(message)
throw new Error(message)}
if(response.logout_success){window.location.href=window.location.href}})})};if(window.location.pathname=='/admin/cash-flow-group/report'){const trashIconBtn=Array.from(document.querySelectorAll(".fa.fa-trash"))
if(trashIconBtn){const launchModal=document.getElementById("launchModal")
const modalContainerLabel=document.getElementById("modalContainerLabel")
const modalBody=document.querySelector(".modal-body")
const saveChanges=document.getElementById("saveChanges")
saveChanges.classList.remove("btn-primary")
saveChanges.classList.add("btn-danger")
saveChanges.innerHTML="Excluir"
const dismissModal=document.getElementById("dismissModal")
dismissModal.innerHTML="Voltar"
const dataDelete={}
trashIconBtn.forEach(function(element){const linkDelete=element.parentElement
linkDelete.addEventListener("click",function(event){event.preventDefault()
const row=this.parentElement.parentElement
let uuidParameter=this.parentElement.previousElementSibling.firstElementChild
uuidParameter=uuidParameter.href.split("/")
uuidParameter=uuidParameter.pop()
let url=`${window.location.origin}/admin/cash-flow-group/remove/${uuidParameter}`
dataDelete.uuidParameter=uuidParameter
dataDelete.url=url
dataDelete.row=row
launchModal.click()})})
launchModal.addEventListener("click",function(){modalContainerLabel.innerHTML="Atenção!"
modalBody.innerHTML=`Você quer mesmo deletar o registro ${dataDelete.uuidParameter}?`})
saveChanges.addEventListener("click",function(){showSpinner(saveChanges)
fetch(`${window.location.origin}/admin/cash-flow-group/remove/${dataDelete.uuidParameter}`,{method:"POST"}).then((response)=>response.json()).then(function(response){let message=""
saveChanges.innerHTML="Excluir"
saveChanges.removeAttribute("disabled")
if(response.error){message=response.error
message=message.charAt(0).toLocaleUpperCase()+message.slice(1)
toastr.error(message)
throw new Error(message)}
if(response.success){message=response.success
message=message.charAt(0).toUpperCase()+message.slice(1)
toastr.success(message)
cashFlowGroupTable.row(dataDelete.row).remove().draw()
dismissModal.click()}})})}};if(window.location.pathname=='/admin/cash-flow/report'){const trashIconBtn=Array.from(document.querySelectorAll(".fa.fa-trash"))
if(trashIconBtn){const launchModal=document.getElementById("launchModal")
const modalContainerLabel=document.getElementById("modalContainerLabel")
const modalBody=document.querySelector(".modal-body")
const saveChanges=document.getElementById("saveChanges")
saveChanges.classList.remove("btn-primary")
saveChanges.classList.add("btn-danger")
saveChanges.innerHTML="Excluir"
const dismissModal=document.getElementById("dismissModal")
dismissModal.innerHTML="Voltar"
const dataDelete={}
trashIconBtn.forEach(function(element){const linkDelete=element.parentElement
linkDelete.addEventListener("click",function(event){event.preventDefault()
const row=this.parentElement.parentElement
let uuidParameter=this.parentElement.previousElementSibling.firstElementChild
uuidParameter=uuidParameter.href.split("/")
uuidParameter=uuidParameter.pop()
let url=`${window.location.origin}/admin/cash-flow/remove/${uuidParameter}`
dataDelete.uuidParameter=uuidParameter
dataDelete.url=url
dataDelete.row=row
launchModal.click()})})
launchModal.addEventListener("click",function(){modalContainerLabel.innerHTML="Atenção!"
modalBody.innerHTML=`Você quer mesmo deletar o registro ${dataDelete.uuidParameter}?`})
saveChanges.addEventListener("click",function(){showSpinner(saveChanges)
fetch(`${window.location.origin}/admin/cash-flow/remove/${dataDelete.uuidParameter}`,{method:"POST"}).then((response)=>response.json()).then(function(response){let message=""
const tFoot=Array.from(document.querySelector("tfoot").firstElementChild.children)
const totalRow=document.querySelector("tfoot").firstElementChild
totalRow.style.color=response.color
saveChanges.innerHTML="Excluir"
saveChanges.removeAttribute("disabled")
tFoot.forEach(function(element){if(element.innerHTML&&element.innerHTML!='Total'){element.innerHTML=response.balance}})
if(response.data_is_empty){message=response.data_is_empty
message=message.charAt(0).toUpperCase()+message.slice(1)
toastr.error(message)
throw new Error(message)}
if(response.cash_flow_data_not_found){message=response.cash_flow_data_not_found
message=message.charAt(0).toUpperCase()+message.slice(1)
toastr.error(message)
throw new Error(message)}
if(response.error){message=response.error
message=message.charAt(0).toUpperCase()+message.slice(1)
toastr.error(message)
throw new Error(message)}
if(response.success){message=response.message
message=message.charAt(0).toUpperCase()+message.slice(1)
toastr.success(message)
cashFlowTable.row(dataDelete.row).remove().draw()
dismissModal.click()}})})}};if(window.location.pathname+window.location.search==`/admin/cash-flow/report${window.location.search}`){const searchCashFlowById=document.getElementById("searchCashFlowById")
searchCashFlowById.addEventListener("submit",function(event){event.preventDefault()
const btnSubmit=this.querySelector("[type='submit']")
showSpinner(btnSubmit)
this.submit()})};let cashFlowParameter=window.location.pathname.split("/")
cashFlowParameter=cashFlowParameter.pop()
if(window.location.pathname==`/admin/cash-flow/update/form/${cashFlowParameter}`){$("#launchValue").maskMoney({allowNegative:!1,thousands:'.',decimal:',',affixesStay:!1})
const cashFlowForm=document.getElementById("cashFlowForm")
cashFlowForm.addEventListener("submit",function(event){event.preventDefault()
const updateBtn=document.getElementById("updateBtn")
if(!this.launchValue.value){toastr.warning("Campo valor de lançamento não pode estar vazio")
throw new Error("Campo valor de lançamento não pode estar vazio")}
if(!this.releaseHistory.value){toastr.warning("Campo histórico não pode estar vazio")
throw new Error("Campo histórico não pode estar vazio")}
if(!this.createdAt.value){toastr.warning("Campo data não pode estar vazio")
throw new Error("Campo data não pode estar vazio")}
if(!this.entryType.value){toastr.warning("Tipo de entrada inválida")
throw new Error("Tipo de entrada inválida")}
showSpinner(updateBtn)
const form=new FormData(this)
fetch(window.location.href,{method:"POST",body:form}).then((response)=>response.json()).then(function(response){let message=''
if(response.empty_cash_flow){updateBtn.innerHTML='Atualizar'
updateBtn.removeAttribute("disabled")
message=response.empty_cash_flow
message=message.charAt(0).toUpperCase()+message.slice(1)
toastr.error(message)
throw new Error(message)}
if(response.user_not_exists){updateBtn.innerHTML='Atualizar'
updateBtn.removeAttribute("disabled")
message=response.user_not_exists
message=message.charAt(0).toUpperCase()+message.slice(1)
toastr.error(message)
throw new Error(message)}
if(response.data_is_empty){updateBtn.innerHTML='Atualizar'
updateBtn.removeAttribute("disabled")
message=response.data_is_empty
message=message.charAt(0).toUpperCase()+message.slice(1)
toastr.error(message)
throw new Error(message)}
if(response.cash_flow_data_not_found){updateBtn.innerHTML='Atualizar'
updateBtn.removeAttribute("disabled")
message=response.cash_flow_data_not_found
message=message.charAt(0).toUpperCase()+message.slice(1)
toastr.error(message)
throw new Error(message)}
if(response.invalid_date){updateBtn.innerHTML='Atualizar'
updateBtn.removeAttribute("disabled")
message=response.invalid_date
message=message.charAt(0).toUpperCase()+message.slice(1)
toastr.error(message)
throw new Error(message)}
if(response.error){updateBtn.innerHTML='Atualizar'
updateBtn.removeAttribute("disabled")
message=response.error
message=message.charAt(0).toUpperCase()+message.slice(1)
toastr.error(message)
throw new Error(message)}
if(response.success){window.location.href=response.url}})})};let cashFlowGroupParameter=window.location.pathname.split("/")
cashFlowGroupParameter=cashFlowGroupParameter.pop()
if(window.location.pathname==`/admin/cash-flow-group/update/form/${cashFlowGroupParameter}`){const cashFlowGroupForm=document.getElementById("cashFlowGroupForm")
cashFlowGroupForm.addEventListener("submit",function(event){event.preventDefault()
const btnSubmit=this.querySelector("[type='submit']")
if(!this.accountGroup.value){toastr.warning("Campo nome grupo de contas é obrigatório")
throw new Error("Campo nome grupo de contas é obrigatório")}
if(!this.csrfToken.value){toastr.warning("Campo token é obrigatório")
throw new Error("Campo token é obrigatório")}
const form=new FormData(this)
showSpinner(btnSubmit)
fetch(window.location.href,{method:"POST",body:form}).then(response=>response.json()).then(function(response){let message=""
if(response.error){btnSubmit.innerHTML="Atualizar"
btnSubmit.removeAttribute("disabled")
message=response.error
message=message.charAt(0).toUpperCase()+message.slice(1)
toastr.error(message)
throw new Error(message)}
if(response.success){window.location.href=response.url}})})}
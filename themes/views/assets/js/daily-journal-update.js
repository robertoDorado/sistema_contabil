const dailyJournalUuid = Array.isArray(window.location.href.match(/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/)) ?
window.location.href.match(/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/)[0] : ""
const dailyJournalEndpoint = window.location.pathname.replace(/\/\w{8}-\w{4}-\w{4}-\w{4}-\w{12}/, "")

if (dailyJournalEndpoint == "/admin/balance-sheet/daily-journal/form") {
    $("#chartOfAccountSelect").select2()
}
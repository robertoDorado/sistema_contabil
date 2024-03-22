const urlJson = document.getElementById("urlJson").dataset.url
const cashFlowTable = dataTableConfig($("#cashFlowReport"),
{
    "order": [[0, "desc"]],
    "language": {
        "url": urlJson
    },
    "responsive": true,
    "lengthChange": false,
    "autoWidth": false,
    "buttons": [
        "copy",
        {
            extend: "csv",
            charset: 'utf-8',
            bom: true,
            customize: function (csvData) {
                let arrayCsvData = csvData.split('"')
                arrayCsvData = arrayCsvData.map(function (item) {
                    item = item.replace(/^Editar$/, "")
                    item = item.replace(/^Excluir$/, "")
                    return item
                })
                arrayCsvData = arrayCsvData.filter((string) => string)

                for (let i = arrayCsvData.length - 1; i > 0; i--) {
                    if (arrayCsvData[i] == arrayCsvData[i - 1]) {
                        arrayCsvData.splice(i - 1, 2)
                    }
                }

                let templateCsv = arrayCsvData.join('"')
                if (!templateCsv.startsWith('"')) {
                    templateCsv = `"${templateCsv}`
                }

                if (!templateCsv.endsWith('"')) {
                    templateCsv = `${templateCsv}"`
                }
                return templateCsv
            }
        },
        {
            extend: "excel",
            customizeData: function (xlsxData) {
                let balance = 0
                let arrayXlsxData = Array.from(xlsxData.body)

                arrayXlsxData = arrayXlsxData.map(function (row) {
                    row[5] = parseFloat(row[5].replace("R$", "")
                        .replace(".", "").replace(",", ".").trim())
                    row = row.filter((data) => data)
                    balance += row[5]
                    return row
                })

                arrayXlsxData.push(['Total', '', '', '', '', balance])
                xlsxData.header = xlsxData.header.filter((data) => data != 'Editar' && data != 'Excluir')
                xlsxData.body = arrayXlsxData
            }
        },
        {
            extend: "pdf",
            customize: function (pdfData) {
                let arrayPdfData = Array.from(pdfData.content[1].table.body)
                let header = arrayPdfData.shift()
                let balance = 0

                arrayPdfData = arrayPdfData.map(function (row) {
                    row[5].text = parseFloat(row[5].text.replace("R$", "")
                    .replace(".", "").replace(",", ".").trim())
                    balance += row[5].text

                    row[5].text = row[5].text
                    .toLocaleString("pt-br", { "currency": "BRL", "style": "currency" })

                    return row.filter((data) => data.text)
                })

                balance = balance.toLocaleString("pt-br", { "currency": "BRL", "style": "currency" })
                header = header.filter((item) => item.text != "Editar" && item.text != "Excluir")
                
                arrayPdfData.unshift(header)
                arrayPdfData.push(
                    [
                        {
                            text: 'Total',
                            style: 'tableBodyOdd',
                            fillColor: '#f1ff32'
                        },
                        {
                            text: '',
                            style: 'tableBodyOdd',
                            fillColor: '#f1ff32'
                        },
                        {
                            text: '',
                            style: 'tableBodyOdd',
                            fillColor: '#f1ff32'
                        },
                        {
                            text: '',
                            style: 'tableBodyOdd',
                            fillColor: '#f1ff32'
                        },
                        {
                            text: '',
                            style: 'tableBodyOdd',
                            fillColor: '#f1ff32'
                        },
                        {
                            text: balance,
                            style: 'tableBodyOdd',
                            fillColor: '#f1ff32'
                        }
                    ]
                )
                
                pdfData.content[1].table.body = arrayPdfData
            }
        },
        "colvis"
    ],
    "initComplete": function () {
        this.api()
            .buttons()
            .container()
            .appendTo("#widgets .col-md-6:eq(0)");
    }
})
const cashFlowGroupTable = dataTableConfig($("#cashFlowGroupReport"), 
{
    "language": {
        "url": urlJson
    }
})
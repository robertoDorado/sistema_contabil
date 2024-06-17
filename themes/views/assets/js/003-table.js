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
            {
                "extend": 'copyHtml5',
                "title": 'Fluxo de caixa'
            },
            {
                extend: "csvHtml5",
                filename: "Fluxo de caixa",
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
                extend: "excelHtml5",
                filename: "Fluxo de caixa",
                title: "Fluxo de caixa",
                customizeData: function (xlsxData) {
                    let balance = 0
                    let arrayXlsxData = Array.from(xlsxData.body)

                    arrayXlsxData = arrayXlsxData.map(function (row) {
                        row[5] = parseFloat(row[5].replace("R$", "").replace(".", "").replace(",", ".").trim())
                        row[2] = formatDate(row[2])

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
                extend: "pdfHtml5",
                filename: "Fluxo de caixa",
                title: "Fluxo de caixa",
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

                    pdfData.content[1].table.widths = [
                        '16.66%', '16.66%', '16.66%', '16.66%', '16.66%', '16.66%'
                    ];

                    pdfData.content[1].table.body = arrayPdfData
                    var objLayout = {};
                    objLayout['hLineWidth'] = function (i) { return 0.5; };
                    objLayout['vLineWidth'] = function (i) { return 0.5; };
                    objLayout['hLineColor'] = function (i) { return '#aaa'; };
                    objLayout['vLineColor'] = function (i) { return '#aaa'; };
                    objLayout['paddingLeft'] = function (i) { return 4; };
                    objLayout['paddingRight'] = function (i) { return 4; };
                    objLayout['paddingTop'] = function (i) { return 4; };
                    objLayout['paddingBottom'] = function (i) { return 4; };
                    objLayout['fillColor'] = function (i) { return null; };
                    pdfData.content[1].layout = objLayout;
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
const companyReport = dataTableConfig($("#companyReport"),
    {
        "order": [[0, "desc"]],
        "language": {
            "url": urlJson
        },
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "buttons": [
            {
                "extend": 'copyHtml5',
                "title": 'Empresas'
            },
            {
                "extend": 'excelHtml5',
                "filename": "Empresas",
                "title": "Empresas",
                customizeData: function (xlsxData) {
                    xlsxData.header = xlsxData.header.filter((data) => data != 'Editar' && data != 'Excluir')
                }
            },
            {
                "extend": 'csvHtml5',
                "filename": "Empresas",
                "title": "Empresas"
            },
            {
                "extend": 'pdfHtml5',
                "filename": "Empresas",
                "title": 'Empresas',
                customize: function (doc) {
                    doc.content[1].table.body.forEach(function (row) {
                        row.splice(0, 1);
                        row.splice(4, 8);
                        row.splice(6);
                    });

                    var objLayout = {};
                    objLayout['hLineWidth'] = function (i) { return 0.5; };
                    objLayout['vLineWidth'] = function (i) { return 0.5; };
                    objLayout['hLineColor'] = function (i) { return '#aaa'; };
                    objLayout['vLineColor'] = function (i) { return '#aaa'; };
                    objLayout['paddingLeft'] = function (i) { return 4; };
                    objLayout['paddingRight'] = function (i) { return 4; };
                    objLayout['paddingTop'] = function (i) { return 4; };
                    objLayout['paddingBottom'] = function (i) { return 4; };
                    objLayout['fillColor'] = function (i) { return null; };
                    doc.content[1].layout = objLayout;
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
const automaticReconciliationReport = dataTableConfig($("#automaticReconciliationReport"),
    {
        "order": [[0, "desc"]],
        "language": {
            "url": urlJson
        },
        "responsive": true,
        "lengthChange": false,
        "autoWidth": false,
        "buttons": [
            {
                "extend": 'copyHtml5',
                "title": 'Conciliação bancária automática'
            },
            {
                "extend": 'excelHtml5',
                "filename": "Conciliação bancária automática",
                "title": "Conciliação bancária automática",
                customizeData: function (xlsxData) {
                    xlsxData.header = xlsxData.header.filter((data) => data != 'Editar' && data != 'Excluir')
                    xlsxData.body = xlsxData.body.map(function(row) {
                        row[2] = parseFloat(row[2].replace("R$", "").replace(".", "").replace(",", ".").trim())
                        row[0] = formatDate(row[0])
                        return row
                    })
                }
            },
            {
                "extend": 'csvHtml5',
                "filename": "Conciliação bancária automática",
                "title": "Conciliação bancária automática"
            },
            {
                "extend": 'pdfHtml5',
                "filename": "Conciliação bancária automática",
                "title": 'Conciliação bancária automática',
                customize: function (doc) {
                    doc.content[1].table.widths = [
                        '33.33%', '33.33%', '33.33%'
                    ];

                    var objLayout = {};
                    objLayout['hLineWidth'] = function (i) { return 0.5; };
                    objLayout['vLineWidth'] = function (i) { return 0.5; };
                    objLayout['hLineColor'] = function (i) { return '#aaa'; };
                    objLayout['vLineColor'] = function (i) { return '#aaa'; };
                    objLayout['paddingLeft'] = function (i) { return 4; };
                    objLayout['paddingRight'] = function (i) { return 4; };
                    objLayout['paddingTop'] = function (i) { return 4; };
                    objLayout['paddingBottom'] = function (i) { return 4; };
                    objLayout['fillColor'] = function (i) { return null; };
                    doc.content[1].layout = objLayout;
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
const cashFlowGroupDeletedReport = dataTableConfig($("#cashFlowGroupDeletedReport"),
    {
        "language": {
            "url": urlJson
        }
    })
const cashFlowDeletedReport = dataTableConfig($("#cashFlowDeletedReport"), {
    "language": {
        "url": urlJson
    }
})
const companyDeletedReport = dataTableConfig($("#companyDeletedReport"), {
    "language": {
        "url": urlJson
    }
})
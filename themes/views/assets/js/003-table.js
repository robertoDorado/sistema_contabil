const urlJson = document.getElementById("urlJson").dataset.url
const pathnameAllowedButtons = ["/admin/cash-flow/report"]
let buttonsData = []
if (pathnameAllowedButtons.indexOf(window.location.pathname) != -1) {
    buttonsData = [
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
    ]
}
const initComplete = buttonsData.length > 0 ? function () {
    this.api()
        .buttons()
        .container()
        .appendTo("#widgets .col-md-6:eq(0)");
} : null
const cashFlowTable = dataTableConfig($("#cashFlowReport"),
    {
        "columnDefs": [
            {
                "targets": [0],
                "visible": false
            }
        ],
        "order": [[0, "desc"]],
        "language": {
            "url": urlJson
        },
        "responsive": true,
        "autoWidth": false,
        "buttons": buttonsData,
        "initComplete": initComplete
    })
const companyReport = dataTableConfig($("#companyReport"),
    {
        "columnDefs": [
            {
                "targets": [0],
                "visible": false
            }
        ],
        "order": [[0, "desc"]],
        "language": {
            "url": urlJson
        },
        "responsive": true,
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
const automaticReconciliationReportCashFlow = dataTableConfig($("#automaticReconciliationReportCashFlow"),
    {
        "order": [[0, "desc"]],
        "language": {
            "url": urlJson
        },
        "responsive": true,
        "autoWidth": false,
        "buttons": [
            {
                "extend": 'copyHtml5',
                "title": 'Conciliação bancária automática do fluxo de caixa'
            },
            {
                "extend": 'excelHtml5',
                "filename": "Conciliação bancária automática do fluxo de caixa",
                "title": "Conciliação bancária automática do fluxo de caixa",
                customizeData: function (xlsxData) {
                    xlsxData.header = xlsxData.header.filter((data) => data != 'Editar' && data != 'Excluir')
                    xlsxData.body = xlsxData.body.map(function (row) {
                        row[2] = parseFloat(row[2].replace("R$", "").replace(".", "").replace(",", ".").trim())
                        row[0] = formatDate(row[0])
                        return row
                    })
                }
            },
            {
                "extend": 'csvHtml5',
                "filename": "Conciliação bancária automática do fluxo de caixa",
                "title": "Conciliação bancária automática do fluxo de caixa"
            },
            {
                "extend": 'pdfHtml5',
                "filename": "Conciliação bancária automática do fluxo de caixa",
                "title": 'Conciliação bancária automática do fluxo de caixa',
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
        "columnDefs": [
            {
                "targets": [0],
                "visible": false
            }
        ],
        "language": {
            "url": urlJson
        }
    })
const cashFlowGroupDeletedReport = dataTableConfig($("#cashFlowGroupDeletedReport"),
    {
        "columnDefs": [
            {
                "targets": [0],
                "visible": false
            }
        ],
        "language": {
            "url": urlJson
        }
    })
const cashFlowDeletedReport = dataTableConfig($("#cashFlowDeletedReport"), {
    "columnDefs": [
        {
            "targets": [0],
            "visible": false
        }
    ],
    "language": {
        "url": urlJson
    }
})
const companyDeletedReport = dataTableConfig($("#companyDeletedReport"), {
    "columnDefs": [
        {
            "targets": [0],
            "visible": false
        }
    ],
    "language": {
        "url": urlJson
    }
})
const manualReconciliationReportCashFlow = dataTableConfig($("#manualReconciliationReportCashFlow"), {
    "language": {
        "url": urlJson
    }
})
const financialIndicatorsFco = dataTableConfig($("#financialIndicatorsFco"), {
    "lengthChange": false,
    "paging": false,
    "searching": false,
    "info": false,
    "language": {
        "url": urlJson
    }
})
const financialIndicatorsFcl = dataTableConfig($("#financialIndicatorsFcl"), {
    "lengthChange": false,
    "paging": false,
    "searching": false,
    "info": false,
    "language": {
        "url": urlJson
    }
})
const financialIndicatorsFcf = dataTableConfig($("#financialIndicatorsFcf"), {
    "lengthChange": false,
    "paging": false,
    "searching": false,
    "info": false,
    "language": {
        "url": urlJson
    }
})
const financialIndicatorsFci = dataTableConfig($("#financialIndicatorsFci"), {
    "lengthChange": false,
    "paging": false,
    "searching": false,
    "info": false,
    "language": {
        "url": urlJson
    }
})
const financialIndicatorsIcjfc = dataTableConfig($("#financialIndicatorsIcjfc"), {
    "lengthChange": false,
    "paging": false,
    "searching": false,
    "info": false,
    "language": {
        "url": urlJson
    }
})
const financialIndicatorsIcsd = dataTableConfig($("#financialIndicatorsIcsd"), {
    "lengthChange": false,
    "paging": false,
    "searching": false,
    "info": false,
    "language": {
        "url": urlJson
    }
})
const financialIndicatorsIrfc = dataTableConfig($("#financialIndicatorsIrfc"), {
    "lengthChange": false,
    "paging": false,
    "searching": false,
    "info": false,
    "language": {
        "url": urlJson
    }
})
const financialIndicatorsMfc = dataTableConfig($("#financialIndicatorsMfc"), {
    "lengthChange": false,
    "paging": false,
    "searching": false,
    "info": false,
    "language": {
        "url": urlJson
    }
})
const financialIndicatorsCc = dataTableConfig($("#financialIndicatorsCc"), {
    "lengthChange": false,
    "paging": false,
    "searching": false,
    "info": false,
    "language": {
        "url": urlJson
    }
})
const cashFlowProjectionsIncome = dataTableConfig($("#cashFlowProjectionsIncome"),
    {
        "columnDefs": [
            {
                "targets": [0],
                "visible": false
            }
        ],
        "language": {
            "url": urlJson
        },
        "responsive": true,
        "autoWidth": false,
        "buttons": [
            {
                "extend": 'copyHtml5',
                "title": 'Entradas de caixa'
            },
            {
                "extend": 'excelHtml5',
                "filename": "Entradas de caixa",
                "title": "Entradas de caixa"
            },
            {
                "extend": 'csvHtml5',
                "filename": "Entradas de caixa",
                "title": "Entradas de caixa"
            },
            {
                "extend": 'pdfHtml5',
                "filename": "Entradas de caixa",
                "title": 'Entradas de caixa'
            },
            "colvis"
        ],
        "initComplete": function () {
            this.api()
                .buttons()
                .container()
                .appendTo(".cash-flow-projections-income .col-md-6:eq(0)");
        }
    })
const cashFlowProjectionsExpenses = dataTableConfig($("#cashFlowProjectionsExpenses"),
    {
        "columnDefs": [
            {
                "targets": [0],
                "visible": false
            }
        ],
        "language": {
            "url": urlJson
        },
        "responsive": true,
        "autoWidth": false,
        "buttons": [
            {
                "extend": 'copyHtml5',
                "title": 'Saídas de caixa'
            },
            {
                "extend": 'excelHtml5',
                "filename": "Saídas de caixa",
                "title": "Saídas de caixa"
            },
            {
                "extend": 'csvHtml5',
                "filename": "Saídas de caixa",
                "title": "Saídas de caixa"
            },
            {
                "extend": 'pdfHtml5',
                "filename": "Saídas de caixa",
                "title": 'Saídas de caixa'
            },
            "colvis"
        ],
        "initComplete": function () {
            this.api()
                .buttons()
                .container()
                .appendTo(".cash-flow-projections-expenses .col-md-6:eq(0)");
        }
    })
const cashFlowProjections = dataTableConfig($("#cashFlowProjections"),
    {
        "columnDefs": [
            {
                "targets": [0],
                "visible": false
            }
        ],
        "language": {
            "url": urlJson
        },
        "responsive": true,
        "autoWidth": false,
        "buttons": [
            {
                "extend": 'copyHtml5',
                "title": 'Projeção de fluxo de caixa'
            },
            {
                "extend": 'excelHtml5',
                "filename": "Projeção de fluxo de caixa",
                "title": "Projeção de fluxo de caixa"
            },
            {
                "extend": 'csvHtml5',
                "filename": "Projeção de fluxo de caixa",
                "title": "Projeção de fluxo de caixa"
            },
            {
                "extend": 'pdfHtml5',
                "filename": "Projeção de fluxo de caixa",
                "title": 'Projeção de fluxo de caixa'
            },
            "colvis"
        ],
        "initComplete": function () {
            this.api()
                .buttons()
                .container()
                .appendTo(".cash-flow-projections .col-md-6:eq(0)");
        }
    })
const cashFlowBudget = dataTableConfig($("#cashFlowBudget"),
    {
        "ordering": false,
        "pageLength": 100,
        "searching": false,
        "language": {
            "url": urlJson
        },
        "responsive": true,
        "autoWidth": false,
        "buttons": [
            {
                "extend": 'copyHtml5',
                "title": 'Orçamento de caixa'
            },
            {
                "extend": 'excelHtml5',
                "filename": "Orçamento de caixa",
                "title": "Orçamento de caixa"
            },
            {
                "extend": 'csvHtml5',
                "filename": "Orçamento de caixa",
                "title": "Orçamento de caixa"
            },
            {
                "extend": 'pdfHtml5',
                "filename": "Orçamento de caixa",
                "title": 'Orçamento de caixa'
            },
            "colvis"
        ],
        "initComplete": function () {
            this.api()
                .buttons()
                .container()
                .appendTo(".cash-flow-projections .col-md-6:eq(0)");
        }
    })
const cashFlowVariation = dataTableConfig($("#cashFlowVariation"),
    {
        "columnDefs": [
            {
                "targets": [0],
                "visible": false
            }
        ],
        "searching": false,
        "language": {
            "url": urlJson
        },
        "responsive": true,
        "autoWidth": false,
        "buttons": [
            {
                "extend": 'copyHtml5',
                "title": 'Notas explicativas'
            },
            {
                "extend": 'excelHtml5',
                "filename": "Variação de caixa",
                "title": "Variação de caixa",
                customizeData: function (xlsxData) {
                    xlsxData.header = xlsxData.header.filter((data) => data != 'Editar' && data != 'Excluir')
                }
            },
            {
                "extend": 'csvHtml5',
                "filename": "Variação de caixa",
                "title": "Variação de caixa"
            },
            {
                "extend": 'pdfHtml5',
                "filename": "Variação de caixa",
                "title": 'Variação de caixa',
                customize: function (doc) {
                    doc.content[1].table.body.forEach(function (row) {
                        row.splice(2, 3);
                    });

                    doc.content[1].table.widths = [
                        '50%', '50%'
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
const cashFlowVariationBackup = dataTableConfig($("#cashFlowVariationBackup"),
    {
        "columnDefs": [
            {
                "targets": [0],
                "visible": false
            }
        ],
        "searching": false,
        "language": {
            "url": urlJson
        },
        "responsive": true,
        "autoWidth": false,
        "buttons": [
            {
                "extend": 'copyHtml5',
                "title": 'Variação de caixa'
            },
            {
                "extend": 'excelHtml5',
                "filename": "Variação de caixa",
                "title": "Variação de caixa",
                customizeData: function (xlsxData) {
                    xlsxData.header = xlsxData.header.filter((data) => data != 'Editar' && data != 'Excluir')
                }
            },
            {
                "extend": 'csvHtml5',
                "filename": "Variação de caixa",
                "title": "Variação de caixa"
            },
            {
                "extend": 'pdfHtml5',
                "filename": "Variação de caixa",
                "title": 'Variação de caixa',
                customize: function (doc) {
                    doc.content[1].table.body.forEach(function (row) {
                        row.splice(2, 3);
                    });

                    doc.content[1].table.widths = [
                        '50%', '50%'
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
const cashVariationAnalysis = dataTableConfig($("#cashVariationAnalysis"),
    {
        "ordering": false,
        "pageLength": 100,
        "searching": false,
        "language": {
            "url": urlJson
        },
        "responsive": true,
        "autoWidth": false,
        "buttons": [
            {
                "extend": 'copyHtml5',
                "title": 'Variação de caixa'
            },
            {
                "extend": 'excelHtml5',
                "filename": "Variação de caixa",
                "title": "Variação de caixa",
                customizeData: function (xlsxData) {
                    xlsxData.header = xlsxData.header.filter((data) => data != 'Editar' && data != 'Excluir')
                }
            },
            {
                "extend": 'csvHtml5',
                "filename": "Variação de caixa",
                "title": "Variação de caixa"
            },
            {
                "extend": 'pdfHtml5',
                "filename": "Variação de caixa",
                "title": 'Variação de caixa',
                customize: function (doc) {
                    doc.content[1].table.body.forEach(function (row) {
                        row.splice(2, 3);
                    });

                    doc.content[1].table.widths = [
                        '50%', '50%'
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
const cashFlowExplanatoryNotesReport = dataTableConfig($("#cashFlowExplanatoryNotesReport"),
    {
        "columnDefs": [
            {
                "targets": [0],
                "visible": false
            }
        ],
        "language": {
            "url": urlJson
        },
        "responsive": true,
        "autoWidth": false,
        "buttons": [
            {
                "extend": 'copyHtml5',
                "title": 'Notas explicativas do fluxo de caixa'
            },
            {
                "extend": 'excelHtml5',
                "filename": "Notas explicativas do fluxo de caixa",
                "title": "Notas explicativas do fluxo de caixa",
                customizeData: function (xlsxData) {
                    xlsxData.header = xlsxData.header.filter((data) => data != 'Editar' && data != 'Excluir')
                }
            },
            {
                "extend": 'csvHtml5',
                "filename": "Notas explicativas do fluxo de caixa",
                "title": "Notas explicativas do fluxo de caixa"
            },
            {
                "extend": 'pdfHtml5',
                "filename": "Notas explicativas do fluxo de caixa",
                "title": 'Notas explicativas do fluxo de caixa',
                customize: function (doc) {
                    doc.content[1].table.body.forEach(function (row) {
                        row.splice(5, 2);
                    });

                    doc.content[1].table.widths = [
                        '20%', '20%', '20%', '20%', '20%'
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
const cashFlowExplanatoryNotesBackup = dataTableConfig($("#cashFlowExplanatoryNotesBackup"),
    {
        "language": {
            "url": urlJson
        },
        "responsive": true,
        "autoWidth": false,
        "initComplete": function () {
            this.api()
                .buttons()
                .container()
                .appendTo("#widgets .col-md-6:eq(0)");
        }
    })
const historyAuditReport = dataTableConfig($("#historyAuditReport"),
    {
        "columnDefs": [
            {
                "targets": [0],
                "visible": false,
            },
            {
                "width": "12%",
                "targets": [3]
            }
        ],
        "language": {
            "url": urlJson
        },
        "responsive": true,
        "autoWidth": false,
        "buttons": [
            {
                "extend": 'copyHtml5',
                "title": 'Histórico da auditoria'
            },
            {
                "extend": 'excelHtml5',
                "filename": "Histórico da auditoria",
                "title": "Histórico da auditoria",
                customizeData: function (xlsxData) {
                    xlsxData.header = xlsxData.header.filter((data) => data != 'Editar' && data != 'Excluir')
                }
            },
            {
                "extend": 'csvHtml5',
                "filename": "Histórico da auditoria",
                "title": "Histórico da auditoria"
            },
            {
                "extend": 'pdfHtml5',
                "filename": "Histórico da auditoria",
                "title": 'Histórico da auditoria',
                customize: function (doc) {
                    doc.content[1].table.body.forEach(function (row) {
                        row.splice(6, 1);
                    });

                    doc.content[1].table.widths = [
                        '16.66%', '16.66%', '16.66%', '16.66%', '16.66%', '16.66%'
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
const historyAuditBackup = dataTableConfig($("#historyAuditBackup"),
    {
        "columnDefs": [
            {
                "targets": [0],
                "visible": false,
            },
            {
                "width": "12%",
                "targets": [3]
            }
        ],
        "language": {
            "url": urlJson
        },
        "responsive": true,
        "autoWidth": false,
        "initComplete": function () {
            this.api()
                .buttons()
                .container()
                .appendTo("#widgets .col-md-6:eq(0)");
        }
    })
const chartOfAccountTable = dataTableConfig($("#chartOfAccountTable"),
    {
        "columnDefs": [
            {
                "targets": [0],
                "visible": false,
            }
        ],
        "language": {
            "url": urlJson
        },
        "responsive": true,
        "autoWidth": false,
        "buttons": [
            {
                "extend": 'copyHtml5',
                "title": 'Plano de contas'
            },
            {
                "extend": 'excelHtml5',
                "filename": "Plano de contas",
                "title": "Plano de contas",
                customizeData: function (xlsxData) {
                    xlsxData.header = xlsxData.header.filter((data) => data != 'Editar' && data != 'Excluir')
                }
            },
            {
                "extend": 'csvHtml5',
                "filename": "Plano de contas",
                "title": "Plano de contas"
            },
            {
                "extend": 'pdfHtml5',
                "filename": "Plano de contas",
                "title": 'Plano de contas',
                customize: function (doc) {
                    doc.content[1].table.body.forEach(function (row) {
                        row.splice(4, 2);
                    });

                    doc.content[1].table.widths = [
                        '25%', '25%', '25%', '25%'
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
const chartOfAccountGroup = dataTableConfig($("#chartOfAccountGroup"),
    {
        "columnDefs": [
            {
                "targets": [0],
                "visible": false,
            }
        ],
        "language": {
            "url": urlJson
        },
        "responsive": true,
        "autoWidth": false,
        "buttons": [
            {
                "extend": 'copyHtml5',
                "title": 'Categoria plano de contas'
            },
            {
                "extend": 'excelHtml5',
                "filename": "Categoria plano de contas",
                "title": "Categoria plano de contas",
                customizeData: function (xlsxData) {
                    xlsxData.header = xlsxData.header.filter((data) => data != 'Editar' && data != 'Excluir')
                }
            },
            {
                "extend": 'csvHtml5',
                "filename": "Categoria plano de contas",
                "title": "Categoria plano de contas"
            },
            {
                "extend": 'pdfHtml5',
                "filename": "Categoria plano de contas",
                "title": 'Categoria plano de contas',
                customize: function (doc) {
                    doc.content[1].table.body.forEach(function (row) {
                        row.splice(3, 2);
                    });

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
const chartOfAccountBackup = dataTableConfig($("#chartOfAccountBackup"),
    {
        "columnDefs": [
            {
                "targets": [0],
                "visible": false,
            }
        ],
        "language": {
            "url": urlJson
        },
        "responsive": true,
        "autoWidth": false
    })
const chartOfAccountGroupBackup = dataTableConfig($("#chartOfAccountGroupBackup"),
    {
        "columnDefs": [
            {
                "targets": [0],
                "visible": false,
            }
        ],
        "language": {
            "url": urlJson
        },
        "responsive": true,
        "autoWidth": false
    })

const balanceSheetOptions = function (fileName, selectorName, isVisibleFirstColumn = false, pdfFileType = "default") {
    const verifyFileType = {
        "default": ["33.33%", "33.33%", "33.33%"],
        "total": ["50%", "50%"]
    }
    const columnDefs = isVisibleFirstColumn ?
        [
            {
                "targets": [0],
                "visible": true,
                "width": "50%"
            }
        ] :
        [
            {
                "targets": [0],
                "visible": false,
            }
        ]

    return {
        "ordering": false,
        "searching": false,
        "lengthChange": false,
        "paging": false,
        "columnDefs": columnDefs,
        "language": {
            "url": urlJson
        },
        "responsive": true,
        "autoWidth": false,
        "buttons": [
            {
                "extend": 'copyHtml5',
                "title": fileName
            },
            {
                "extend": 'excelHtml5',
                "filename": fileName,
                "title": fileName
            },
            {
                "extend": 'csvHtml5',
                "filename": fileName,
                "title": fileName
            },
            {
                "extend": 'pdfHtml5',
                "filename": fileName,
                "title": fileName,
                customize: function (doc) {
                    if (pdfFileType == "default") {
                        doc.content[1].table.body.forEach(function (row) {
                            row.splice(0, 1);
                        });
                    }

                    doc.content[1].table.widths = verifyFileType[pdfFileType];

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
                .appendTo(`${selectorName} .col-md-6:eq(0)`);
        }
    }
}

const currentAssets = dataTableConfig($("#currentAssets"), balanceSheetOptions("Ativo circulante", "#currentAssetsWidgets"))
const nonCurrentAssets = dataTableConfig($("#nonCurrentAssets"), balanceSheetOptions("Ativo não circulante", "#nonCurrentAssetsWidgets"))
const currentLiabilities = dataTableConfig($("#currentLiabilities"), balanceSheetOptions("Passivo circulante", "#currentLiabilitiesWidgets"))
const nonCurrentLiabilities = dataTableConfig($("#nonCurrentLiabilities"), balanceSheetOptions("Passivo não circulante", "#nonCurrentLiabilitiesWidgets"))
const shareholdersEquity = dataTableConfig($("#shareholdersEquity"), balanceSheetOptions("Patrimônio líquido", "#shareholdersEquityWidgets"))
const accountingCalculation = dataTableConfig($("#accountingCalculation"), balanceSheetOptions("Apauração contabil", "#accountingCalculationWidget", true, "total"))
const balanceSheetReport = dataTableConfig($("#balanceSheetReport"), balanceSheetOptions("Balanço patrimonial", "#balanceSheetReportWidgets"))

const dailyJournalReport = dataTableConfig($("#dailyJournalReport"),
    {
        "ordering": false,
        "columnDefs": [
            {
                "targets": [0],
                "visible": false,
            },
            {
                "targets": [4],
                "width": "15%"
            }
        ],
        "language": {
            "url": urlJson
        },
        "responsive": true,
        "autoWidth": false,
        "buttons": [
            {
                "extend": 'copyHtml5',
                "title": 'Livro diário'
            },
            {
                "extend": 'excelHtml5',
                "filename": "Livro diário",
                "title": "Livro diário",
                customizeData: function (xlsxData) {
                    xlsxData.header = xlsxData.header.filter((data) => data != 'Editar' && data != 'Excluir')
                }
            },
            {
                "extend": 'csvHtml5',
                "filename": "Livro diário",
                "title": "Livro diário"
            },
            {
                "extend": 'pdfHtml5',
                "filename": "Livro diário",
                "title": 'Livro diário',
                customize: function (doc) {
                    doc.content[1].table.body.forEach(function (row) {
                        row.splice(0, 1);
                        row.splice(5, 2);
                    });

                    doc.content[1].table.widths = [
                        '20%', '20%', '20%', '20%', '20%'
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
const dailyJournalBackup = dataTableConfig($("#dailyJournalBackup"),
    {
        "ordering": false,
        "columnDefs": [
            {
                "targets": [0],
                "visible": false,
            },
            {
                "targets": [4],
                "width": "15%"
            }
        ],
        "language": {
            "url": urlJson
        },
        "responsive": true,
        "autoWidth": false,
        "buttons": [
            {
                "extend": 'copyHtml5',
                "title": 'Livro diário'
            },
            {
                "extend": 'excelHtml5',
                "filename": "Livro diário",
                "title": "Livro diário",
                customizeData: function (xlsxData) {
                    xlsxData.header = xlsxData.header.filter((data) => data != 'Editar' && data != 'Excluir')
                }
            },
            {
                "extend": 'csvHtml5',
                "filename": "Livro diário",
                "title": "Livro diário"
            },
            {
                "extend": 'pdfHtml5',
                "filename": "Livro diário",
                "title": 'Livro diário',
                customize: function (doc) {
                    doc.content[1].table.body.forEach(function (row) {
                        row.splice(0, 1);
                        row.splice(5, 2);
                    });

                    doc.content[1].table.widths = [
                        '20%', '20%', '20%', '20%', '20%'
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
const trialBalanceReport = dataTableConfig($("#trialBalanceReport"),
    {
        "ordering": false,
        "columnDefs": [
            {
                "targets": [0],
                "visible": false,
            }
        ],
        "language": {
            "url": urlJson
        },
        "responsive": true,
        "autoWidth": false,
        "buttons": [
            {
                "extend": 'copyHtml5',
                "title": 'Balancete de verificação'
            },
            {
                "extend": 'excelHtml5',
                "filename": "Balancete de verificação",
                "title": "Balancete de verificação",
                customizeData: function (xlsxData) {
                    xlsxData.header = xlsxData.header.filter((data) => data != 'Editar' && data != 'Excluir')
                }
            },
            {
                "extend": 'csvHtml5',
                "filename": "Balancete de verificação",
                "title": "Balancete de verificação"
            },
            {
                "extend": 'pdfHtml5',
                "filename": "Balancete de verificação",
                "title": 'Balancete de verificação',
                customize: function (doc) {
                    doc.content[1].table.body.forEach(function (row) {
                        row.splice(0, 1);
                    });

                    doc.content[1].table.widths = [
                        '25%', '25%', '25%', '25%'
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
const generalLedgeReport = dataTableConfig($("#generalLedgeReport"),
    {
        "ordering": false,
        "columnDefs": [
            {
                "targets": [0],
                "visible": false,
            },
            {
                "targets": [5, 6, 7],
                "width": "12%"
            }
        ],
        "language": {
            "url": urlJson
        },
        "responsive": true,
        "autoWidth": false,
        "buttons": [
            {
                "extend": 'copyHtml5',
                "title": 'Livro razão'
            },
            {
                "extend": 'excelHtml5',
                "filename": "Livro razão",
                "title": "Livro razão",
                customizeData: function (xlsxData) {
                    xlsxData.header = xlsxData.header.filter((data) => data != 'Editar' && data != 'Excluir')
                }
            },
            {
                "extend": 'csvHtml5',
                "filename": "Livro razão",
                "title": "Livro razão"
            },
            {
                "extend": 'pdfHtml5',
                "filename": "Livro razão",
                "title": 'Livro razão',
                customize: function (doc) {
                    doc.content[1].table.body.forEach(function (row) {
                        row.splice(0, 1);
                    });

                    doc.content[1].table.widths = [
                        '14.28%', '14.28%', '14.28%', '14.28%', '14.28%', '14.28%', '14.28%'
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
const incomeStatement = dataTableConfig($("#incomeStatement"),
    {
        "paging": false,
        "pageLength": 100,
        "ordering": false,
        "language": {
            "url": urlJson
        },
        "responsive": true,
        "autoWidth": false,
        "buttons": [
            {
                "extend": 'copyHtml5',
                "title": 'Livro razão'
            },
            {
                "extend": 'excelHtml5',
                "filename": "Livro razão",
                "title": "Livro razão",
                customizeData: function (xlsxData) {
                    xlsxData.header = xlsxData.header.filter((data) => data != 'Editar' && data != 'Excluir')
                }
            },
            {
                "extend": 'csvHtml5',
                "filename": "Livro razão",
                "title": "Livro razão"
            },
            {
                "extend": 'pdfHtml5',
                "filename": "Livro razão",
                "title": 'Livro razão',
                customize: function (doc) {
                    doc.content[1].table.body.forEach(function (row) {
                        row.splice(0, 1);
                    });

                    doc.content[1].table.widths = [
                        '14.28%', '14.28%', '14.28%', '14.28%', '14.28%', '14.28%', '14.28%'
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
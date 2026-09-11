document.addEventListener("DOMContentLoaded", function(e) {
    var t = document.querySelector(".datatables-users");
    t = (t && new DataTable(t, {
        layout: {
            topStart: {
                rowClass: "row mx-3 my-0 justify-content-between",
                features: [{
                    pageLength: {
                        menu: [10, 25, 50, 100],
                        text: "_MENU_"
                    },
                    buttons: [
                        {
                            extend: 'copyHtml5',
                            exportOptions: {
                                columns: [0, ':visible']
                            }
                        },
                        {
                            extend: 'excelHtml5',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        // {
                        //     extend: 'pdfHtml5',
                        //     exportOptions: {
                        //         columns: [0, 1, 2, 5]
                        //     }
                        // },
                        'colvis'
                    ]
                }]
            },
            topEnd: {
                features: [{
                    search: {
                        placeholder: "Search Data",
                        text: "_INPUT_"
                    }
                }]
            },
            bottomStart: {
                rowClass: "row mx-3 justify-content-between",
                features: ["info"]
            },
            bottomEnd: {
                paging: {
                    firstLast: !1
                }
            },
            language: {
                sLengthMenu: "_MENU_",
                search: "",
                searchPlaceholder: "Search User",
                paginate: {
                    next: '<i class="icon-base bx bx-chevron-right icon-18px"></i>',
                    previous: '<i class="icon-base bx bx-chevron-left icon-18px"></i>'
                }
            },
            responsive: {
                details: {
                    display: DataTable.Responsive.display.modal({
                        header: function(e) {
                            return "Details of " + e.data().full_name
                        }
                    }),
                    type: "column",
                    renderer: function(e, t, a) {
                        var s, n, o, a = a.map(function(e) {
                            return "" !== e.title ? `<tr data-dt-row="${e.rowIndex}" data-dt-column="${e.columnIndex}">
                      <td>${e.title}:</td>
                      <td>${e.data}</td>
                    </tr>` : ""
                        }).join("");
                        return !!a && ((s = document.createElement("div")).classList.add("table-responsive"), n = document.createElement("table"), s.appendChild(n), n.classList.add("table"), (o = document.createElement("tbody")).innerHTML = a, n.appendChild(o), s)
                    }
                }
            },
        },
    }));
    var d = document.querySelector(".datatables-fixed");
    d = (d && new DataTable(d,{
        layout: {
            topStart: {
                rowClass: "row card-header pt-0 pb-0",
                features: [{
                    pageLength: {
                        menu: [10, 25, 50, 100],
                        text: "_MENU_"
                    },
                    buttons: [
                        {
                            extend: 'copyHtml5',
                            exportOptions: {
                                columns: [0, ':visible']
                            }
                        },
                        {
                            extend: 'excelHtml5',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            exportOptions: {
                                columns: [0, 1, 2, 5]
                            }
                        },
                        'colvis'
                    ]
                }]
            },
            topEnd: {
                search: {
                    placeholder: "Search Data"
                }
            },
            bottomStart: {
                rowClass: "row mx-3 justify-content-between",
                features: ["info"]
            },
            bottomEnd: {
                paging: {
                    firstLast: !1
                }
            }
        },
        scrollY: 450,
        scrollX: !0,
        scrollCollapse: !0,
        paging: !1,
        info: true,
        ordering: false
        // fixedColumns: {
        //     start: 1
        // }
    }));

    var d = document.querySelector(".datatables-fixed1");
    d = (d && new DataTable(d,{
        layout: {
            topStart: {
                rowClass: "row card-header pt-0 pb-0",
                features: [{
                    buttons: [
                        {
                            extend: 'copyHtml5',
                            exportOptions: {
                                columns: [0, ':visible']
                            }
                        },
                        {
                            extend: 'excelHtml5',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            exportOptions: {
                                columns: [0, 1, 2, 5]
                            }
                        },
                        'colvis'
                    ]
                }]
            },
            topEnd: {
                search: {
                    placeholder: "Search Data"
                }
            },
            bottomStart: {
                rowClass: "row mx-3 justify-content-between",
                features: ["info"]
            },
            bottomEnd: {
                paging: {
                    firstLast: !1
                }
            }
        },
        scrollY: 450,
        scrollX: !0,
        scrollCollapse: !0,
        paging: !1,
        info: true,
        // fixedColumns: {
        //     start: 1
        // }
    }));

    var d = document.querySelector(".datatables-fixed2");
    d = (d && new DataTable(d,{
        layout: {
            topStart: {
                rowClass: "row card-header pt-0 pb-0",
                features: [{
                    buttons: [
                        {
                            extend: 'copyHtml5',
                            exportOptions: {
                                columns: [0, ':visible']
                            }
                        },
                        {
                            extend: 'excelHtml5',
                            exportOptions: {
                                columns: ':visible'
                            }
                        },
                        {
                            extend: 'pdfHtml5',
                            exportOptions: {
                                columns: [0, 1, 2, 5]
                            }
                        },
                        'colvis'
                    ]
                }]
            },
            topEnd: {
                search: {
                    placeholder: "Search Data"
                }
            },
            bottomStart: {
                rowClass: "row mx-3 justify-content-between",
                features: ["info"]
            },
            bottomEnd: {
                paging: {
                    firstLast: !1
                }
            }
        },
        scrollY: 450,
        scrollX: !0,
        scrollCollapse: !0,
        paging: !1,
        info: true,
        // fixedColumns: {
        //     start: 1
        // }
    }));

    const dt_basic_table = document.querySelector('.datatables-basic');
    let dt_basic;
    if(dt_basic_table){
        let tableTitle = document.createElement('h5');
        tableTitle.classList.add('card-title', 'mb-0', 'text-md-start', 'text-center');
        // tableTitle.innerHTML = 'DataTable with Buttons';
        dt_basic = new DataTable(dt_basic_table, {
            layout: {
                top2Start: {
                rowClass: 'row card-header mx-0 px-2',
                features: [tableTitle]
                },
                top2End: {
                features: [
                    {
                    buttons: [
                        {
                        extend: 'collection',
                        className: 'btn btn-label-primary dropdown-toggle me-4 waves-effect border-none',
                        text: '<span class="d-flex align-items-center gap-2"><i class="icon-base ri ri-external-link-line icon-18px"></i> <span class="d-none d-sm-inline-block">Export</span></span>',
                        buttons: [
                            {
                                extend: 'print',
                                text: `<span class="d-flex align-items-center"><i class="icon-base ri ri-printer-line me-1"></i>Print</span>`,
                                className: 'dropdown-item',
                                exportOptions: {
                                    columns: [3, 4, 5, 6, 7],
                                    format: {
                                    body: function (inner, coldex, rowdex) {
                                        if (inner.length <= 0) return inner;

                                        // Check if inner is HTML content
                                        if (inner.indexOf('<') > -1) {
                                        const parser = new DOMParser();
                                        const doc = parser.parseFromString(inner, 'text/html');

                                        // Get all text content
                                        let text = '';

                                        // Handle specific elements
                                        const userNameElements = doc.querySelectorAll('.user-name');
                                        if (userNameElements.length > 0) {
                                            userNameElements.forEach(el => {
                                            // Get text from nested structure
                                            const nameText =
                                                el.querySelector('.fw-medium')?.textContent ||
                                                el.querySelector('.d-block')?.textContent ||
                                                el.textContent;
                                            text += nameText.trim() + ' ';
                                            });
                                        } else {
                                            // Get regular text content
                                            text = doc.body.textContent || doc.body.innerText;
                                        }

                                        return text.trim();
                                        }

                                        return inner;
                                    }
                                    }
                                },
                                customize: function (win) {
                                    win.document.body.style.color = config.colors.headingColor;
                                    win.document.body.style.borderColor = config.colors.borderColor;
                                    win.document.body.style.backgroundColor = config.colors.bodyBg;
                                    const table = win.document.body.querySelector('table');
                                    table.classList.add('compact');
                                    table.style.color = 'inherit';
                                    table.style.borderColor = 'inherit';
                                    table.style.backgroundColor = 'inherit';
                                }
                            },
                            {
                            extend: 'csv',
                            text: `<span class="d-flex align-items-center"><i class="icon-base ri ri-file-text-line me-1"></i>Csv</span>`,
                            className: 'dropdown-item',
                            exportOptions: {
                                columns: [3, 4, 5, 6, 7],
                                format: {
                                    body: function (inner, coldex, rowdex) {
                                        if (inner.length <= 0) return inner;

                                        // Parse HTML content
                                        const parser = new DOMParser();
                                        const doc = parser.parseFromString(inner, 'text/html');

                                        let text = '';

                                        // Handle user-name elements specifically
                                        const userNameElements = doc.querySelectorAll('.user-name');
                                        if (userNameElements.length > 0) {
                                        userNameElements.forEach(el => {
                                            // Get text from nested structure - try different selectors
                                            const nameText =
                                            el.querySelector('.fw-medium')?.textContent ||
                                            el.querySelector('.d-block')?.textContent ||
                                            el.textContent;
                                            text += nameText.trim() + ' ';
                                        });
                                        } else {
                                        // Handle other elements (status, role, etc)
                                        text = doc.body.textContent || doc.body.innerText;
                                        }

                                        return text.trim();
                                    }
                                }
                            }
                            },
                            {
                            extend: 'excel',
                            text: `<span class="d-flex align-items-center"><i class="icon-base ri ri-file-excel-line me-1"></i>Excel</span>`,
                            className: 'dropdown-item',
                            exportOptions: {
                                columns: [3, 4, 5, 6, 7],
                                format: {
                                body: function (inner, coldex, rowdex) {
                                    if (inner.length <= 0) return inner;

                                    // Parse HTML content
                                    const parser = new DOMParser();
                                    const doc = parser.parseFromString(inner, 'text/html');

                                    let text = '';

                                    // Handle user-name elements specifically
                                    const userNameElements = doc.querySelectorAll('.user-name');
                                    if (userNameElements.length > 0) {
                                    userNameElements.forEach(el => {
                                        // Get text from nested structure - try different selectors
                                        const nameText =
                                        el.querySelector('.fw-medium')?.textContent ||
                                        el.querySelector('.d-block')?.textContent ||
                                        el.textContent;
                                        text += nameText.trim() + ' ';
                                    });
                                    } else {
                                    // Handle other elements (status, role, etc)
                                    text = doc.body.textContent || doc.body.innerText;
                                    }

                                    return text.trim();
                                }
                                }
                            }
                            },
                            {
                            extend: 'pdf',
                            text: `<span class="d-flex align-items-center"><i class="icon-base ri ri-file-pdf-line me-1"></i>Pdf</span>`,
                            className: 'dropdown-item',
                            exportOptions: {
                                columns: [3, 4, 5, 6, 7],
                                format: {
                                body: function (inner, coldex, rowdex) {
                                    if (inner.length <= 0) return inner;

                                    // Parse HTML content
                                    const parser = new DOMParser();
                                    const doc = parser.parseFromString(inner, 'text/html');

                                    let text = '';

                                    // Handle user-name elements specifically
                                    const userNameElements = doc.querySelectorAll('.user-name');
                                    if (userNameElements.length > 0) {
                                    userNameElements.forEach(el => {
                                        // Get text from nested structure - try different selectors
                                        const nameText =
                                        el.querySelector('.fw-medium')?.textContent ||
                                        el.querySelector('.d-block')?.textContent ||
                                        el.textContent;
                                        text += nameText.trim() + ' ';
                                    });
                                    } else {
                                    // Handle other elements (status, role, etc)
                                    text = doc.body.textContent || doc.body.innerText;
                                    }

                                    return text.trim();
                                }
                                }
                            }
                            },
                            {
                            extend: 'copy',
                            text: `<i class="icon-base ri ri-file-copy-line me-1"></i>Copy`,
                            className: 'dropdown-item',
                            exportOptions: {
                                columns: [3, 4, 5, 6, 7],
                                format: {
                                body: function (inner, coldex, rowdex) {
                                    if (inner.length <= 0) return inner;

                                    // Parse HTML content
                                    const parser = new DOMParser();
                                    const doc = parser.parseFromString(inner, 'text/html');

                                    let text = '';

                                    // Handle user-name elements specifically
                                    const userNameElements = doc.querySelectorAll('.user-name');
                                    if (userNameElements.length > 0) {
                                    userNameElements.forEach(el => {
                                        // Get text from nested structure - try different selectors
                                        const nameText =
                                        el.querySelector('.fw-medium')?.textContent ||
                                        el.querySelector('.d-block')?.textContent ||
                                        el.textContent;
                                        text += nameText.trim() + ' ';
                                    });
                                    } else {
                                    // Handle other elements (status, role, etc)
                                    text = doc.body.textContent || doc.body.innerText;
                                    }

                                    return text.trim();
                                }
                                }
                            }
                            }
                        ]
                        }
                    ]
                    }
                ]
                },
                topStart: {
                    rowClass: 'row m-3 mx-2 my-0 justify-content-between',
                    features: [
                        {
                        pageLength: {
                            menu: [7, 10, 25, 50, 100],
                            text: 'Show_MENU_entries'
                        }
                        }
                    ]
                },
                topEnd: {
                    search: {
                        placeholder: 'Type search here'
                    }
                },
                bottomStart: {
                    rowClass: 'row mx-3 justify-content-between',
                    features: ['info']
                },
                bottomEnd: 'paging'
            },
            displayLength: 7,
            language: {
                paginate: {
                    next: '<i class="icon-base ri ri-arrow-right-s-line scaleX-n1-rtl icon-22px"></i>',
                    previous: '<i class="icon-base ri ri-arrow-left-s-line scaleX-n1-rtl icon-22px"></i>',
                    first: '<i class="icon-base ri ri-skip-back-mini-line scaleX-n1-rtl icon-22px"></i>',
                    last: '<i class="icon-base ri ri-skip-forward-mini-line scaleX-n1-rtl icon-22px"></i>'
                }
            },
            responsive: {
                details: {
                display: DataTable.Responsive.display.modal({
                    header: function (row) {
                    const data = row.data();
                    return 'Details of ' + data['full_name'];
                    }
                }),
                type: 'column',
                renderer: function (api, rowIdx, columns) {
                    const data = columns
                    .map(function (col) {
                        return col.title !== '' // Do not show row in modal popup if title is blank (for check box)
                        ? `<tr data-dt-row="${col.rowIndex}" data-dt-column="${col.columnIndex}">
                            <td>${col.title}:</td>
                            <td>${col.data}</td>
                            </tr>`
                        : '';
                    })
                    .join('');

                    if (data) {
                        const div = document.createElement('div');
                        div.classList.add('table-responsive');
                        const table = document.createElement('table');
                        div.appendChild(table);
                        table.classList.add('table');
                        table.classList.add('datatables-basic');
                        const tbody = document.createElement('tbody');
                        tbody.innerHTML = data;
                        table.appendChild(tbody);
                        return div;
                    }
                    return false;
                }
                }
            },
            initComplete: function (settings, json) {
                $('.card-header').after('<hr class="my-0">');
            }
        });
    }
});

(function ($, elementor) {
    'use strict';
    let afeb_widgetDataTable = function ($scope, $) {

        let init = function ($scope, $) {
            let datatable = $scope.find('.afeb-data-table');
            if (!datatable.length) return;

            let settings = datatable.data('settings');
            let src = settings.src;
            let csv_src = settings.csv_src;

            if (src == 'cstm') {
                let th_len = datatable.find('.afeb-inner-table thead tr th, .afeb-inner-table thead tr td').length;
                datatable.find('.afeb-inner-table tbody tr').each(function () {
                    if ($(this).find('td').length < th_len) {
                        let end_len = th_len - $(this).find('td').length;
                        $(this).append(new Array(++end_len).join('<td></td>'));
                    }
                }), datatable.find('.afeb-inner-table').DataTable(Object.assign(settings));
            } else if (src == 'csv') {
                ({
                    csv_render: function ($optns) {
                        let csv_src = $optns.csv_src || '';
                        let datatable = $optns.datatable;
                        let csv_optns = $optns.csv_optns || {};
                        let tbl = $('<table class="afeb-inner-table cell-border">');

                        datatable.empty().append(tbl);
                        $.when($.get(csv_src)).then(function (csv_str) {
                            let csv_array = $.csv.toArrays(csv_str, csv_optns);
                            let thead = $('<thead></thead>');
                            let tr = $('<tr></tr>');
                            let tbdy = $('<tbody></tbody>');

                            for (let i = 0; i < csv_array[0].length; i++)
                                tr.append($('<th></th>').text(csv_array[0][i]));
                            thead.append(tr), tbl.append(thead);
                            for (let j = 1; j < csv_array.length; j++) {
                                let tr = $('<tr></tr>');
                                for (let k = 0; k < csv_array[j].length; k++) {
                                    let td = $('<td></td>');

                                    td.text(csv_array[j][k]);
                                    tr.append(td);
                                    tbdy.append(tr);
                                }
                            }
                            tbl.append(tbdy), tbl.DataTable(Object.assign(settings));
                        });
                    },
                }.csv_render({
                    csv_src: csv_src,
                    datatable: datatable,
                    csv_optns: {}
                }));
            }
        }

        init($scope, $);
    };

    jQuery(window).on('elementor/frontend/init', function () {
        elementorFrontend.hooks.addAction('frontend/element_ready/afeb_data_table.default', afeb_widgetDataTable);
    });

}(jQuery, window.elementorFrontend));
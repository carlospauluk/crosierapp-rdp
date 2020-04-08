'use strict';

import $ from "jquery";



import 'daterangepicker';

$(document).ready(function () {

    let $datatableItens = $('#datatableItens');

    // declaro antes para poder sobreescrever ali com o extent, no caso de querer mudar alguma coisa (ex.: movimentacaoRecorrentesList.js)
    let defaultParams = {
        paging: false,
        serverSide: false,
        stateSave: true,
        searching: false,
        info: false,
        language: {
            "url": "/build/static/datatables-Portuguese-Brasil.json"
        }
    };

    let datatable = $datatableItens.DataTable(defaultParams);


});
'use strict';

import $ from "jquery";

import Moment from 'moment';

import 'datatables';
import 'datatables.net-bs4/css/dataTables.bootstrap4.css';
import 'datatables/media/css/jquery.dataTables.css';

import 'select2/dist/css/select2.css';
import 'select2';
import 'select2/dist/js/i18n/pt-BR.js';
import 'select2-bootstrap-theme/dist/select2-bootstrap.css';

import 'daterangepicker';

$(document).ready(function () {

    $.fn.select2.defaults.set("theme", "bootstrap");
    $.fn.select2.defaults.set("language", "pt-BR");

    let $formPesquisar = $('#formPesquisar');

    let $btnAnterior = $('#btnAnterior');
    let $btnProximo = $('#btnProximo');

    $btnAnterior.on('click', function () {
        let dtIni = Moment($(this).data('ante-periodoi')).format('DD/MM/YYYY');
        let dtFim = Moment($(this).data('ante-periodof')).format('DD/MM/YYYY');
        $filterDts.val(dtIni + ' - ' + dtFim);
        $formPesquisar.submit();
    });

    $btnProximo.on('click', function () {
        let dtIni = Moment($(this).data('prox-periodoi')).format('DD/MM/YYYY');
        let dtFim = Moment($(this).data('prox-periodof')).format('DD/MM/YYYY');
        $filterDts.val(dtIni + ' - ' + dtFim);
        $formPesquisar.submit();
    });

    let $datatableItens = $('#datatableItens');

    let $filterDts = $('#filterDts').daterangepicker(
        {
            opens: 'left',
            autoApply: true,
            locale: {
                "format": "DD/MM/YYYY",
                "separator": " - ",
                "applyLabel": "Aplicar",
                "cancelLabel": "Cancelar",
                "fromLabel": "De",
                "toLabel": "Até",
                "customRangeLabel": "Custom",
                "daysOfWeek": [
                    "Dom",
                    "Seg",
                    "Ter",
                    "Qua",
                    "Qui",
                    "Sex",
                    "Sáb"
                ],
                "monthNames": [
                    "Janeiro",
                    "Fevereiro",
                    "Março",
                    "Abril",
                    "Maio",
                    "Junho",
                    "Julho",
                    "Agosto",
                    "Setembro",
                    "Outubro",
                    "Novembro",
                    "Dezembro"
                ],
                "firstDay": 0
            },
            ranges: {
                'Hoje': [Moment(), Moment()],
                'Ontem': [Moment().subtract(1, 'days'), Moment().subtract(1, 'days')],
                'Esta quinzena': Moment().format('DD') < 16 ? [Moment().date(1), Moment().date(15)] : [Moment().date(16), Moment().endOf('month')],
                'Próxima quinzena': Moment().format('DD') < 16 ? [Moment().date(1), Moment().date(15)] : [Moment().add(1, 'month').date(1), Moment().add(1, 'month').date(15)],
                'Quinzena anterior': Moment().format('DD') < 16 ? [Moment().subtract(1, 'month').date(16), Moment().subtract(1, 'month').endOf('month')] : [Moment().date(1), Moment().date(15)],
                'Este mês': [Moment().startOf('month'), Moment().endOf('month')],
                'Mês passado': [Moment().subtract(1, 'month').startOf('month'), Moment().subtract(1, 'month').endOf('month')]
            },
            "alwaysShowCalendars": true
        },
        function (start, end, label) {
            console.log("A new date selection was made: " + start.format('YYYY-MM-DD') + ' to ' + end.format('YYYY-MM-DD'));
            $formPesquisar.submit();
        }
    ).on('apply.daterangepicker', function (ev, picker) {
        $formPesquisar.submit();
    });





    let $filter_loja = $('#filter_loja');
    let $filter_grupo = $('#filter_grupo');


    $filter_loja.select2({
        data: $filter_loja.data('options')
    });
    if ($filter_loja.data('val')) {
        $filter_loja.val($filter_loja.data('val')).trigger('change');
    }
    $filter_loja.on('select2:select', function () {
        $formPesquisar.submit();
    });


    $filter_grupo.select2({
        data: $filter_grupo.data('options')
    });
    if ($filter_grupo.data('val')) {
        $filter_grupo.val($filter_grupo.data('val')).trigger('change');
    }
    $filter_grupo.on('select2:select', function () {
        $formPesquisar.submit();
    });

















    // declaro antes para poder sobreescrever ali com o extent, no caso de querer mudar alguma coisa (ex.: movimentacaoRecorrentesList.js)
    let defaultParams = {
        paging: false,
        serverSide: false,
        stateSave: true,
        searching: false,
        language: {
            "url": "/build/static/datatables-Portuguese-Brasil.json"
        }
    };

    let datatable = $datatableItens.DataTable(defaultParams);




});
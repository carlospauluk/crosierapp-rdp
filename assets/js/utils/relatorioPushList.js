'use strict';



import DatatablesJs from "../crosier/DatatablesJs";
import Moment from 'moment';

let listId = "#relatorioPushList";

function getDatatablesColumns() {
    return [
        {
            name: 'e.id',
            data: 'e.id',
            title: 'Id'
        },
        {
            name: 'e.descricao',
            data: 'e',
            title: 'Arquivo',
            render: function (data, type, row) {
                let style = data.abertoEm == null ? 'font-weight: bold' : '';
                let url = Routing.generate('relatorioPush_abrir', {'id': data.id});
                return '<a style="' + style + '" href="' + url + '" onclick="document.location.reload(true);" target="_blank">' + data.descricao + '</a>';
            },
        },
        {
            name: 'e.dtEnvio',
            data: 'e.dtEnvio',
            title: 'Dt Envio',
            render: function (data, type, row) {
                return Moment(data).format('DD/MM/YYYY HH:mm:ss');
            },
            className: 'text-center'

        },
        // {
        //     name: 'e.id',
        //     data: 'e',
        //     title: '',
        //     render: function (data, type, row) {
        //         let colHtml = "";
        //         if ($(listId).data('routeedit')) {
        //             let routeedit = Routing.generate($(listId).data('routeedit'), {id: data.id});
        //             colHtml += DatatablesJs.makeEditButton(routeedit);
        //         }
        //         if ($(listId).data('routedelete')) {
        //             let deleteUrl = Routing.generate($(listId).data('routedelete'), {id: data.id});
        //             let csrfTokenDelete = $(listId).data('crsf-token-delete');
        //             colHtml += DatatablesJs.makeDeleteButton(deleteUrl, csrfTokenDelete);
        //         }
        //         return colHtml;
        //     },
        //     className: 'text-right'
        // }
    ];
}

let time = new Date().getTime();
$(document.body).bind("mousemove keypress", function(e) {
    time = new Date().getTime();
});

function refresh() {
    if(new Date().getTime() - time >= 15000)
        window.location.reload(true);
    else
        setTimeout(refresh, 10000);
}

setTimeout(refresh, 10000);

DatatablesJs.makeDatatableJs(listId, getDatatablesColumns());


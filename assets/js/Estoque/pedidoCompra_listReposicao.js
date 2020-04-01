'use strict';

import routes from '../../static/fos_js_routes.json';
import Routing from '../../../vendor/friendsofsymfony/jsrouting-bundle/Resources/public/js/router.min.js';
import Moment from 'moment';


import Numeral from 'numeral';
import 'numeral/locales/pt-br.js';

import $ from "jquery";
import toastrr from "toastr";

Routing.setRoutingData(routes);

Numeral.locale('pt-br');

$(document).ready(function () {

    let $selTodasMovs = $('#selTodasMovs');
    let $btnImprimirListaReposicao = $('#btnImprimirListaReposicao');


    $btnImprimirListaReposicao.click(function () {
        let url = $(this).data('url');
        let form = $('<form>').attr("method", "post").attr(
            "action", url);
        form.append($('.movSel').clone());
        // simuland o clique
        form.append('<input type="hidden" name="btnImprimirListaReposicao" value="1" />');
        $(form).appendTo('body').submit();

    });


});
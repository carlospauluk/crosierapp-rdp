'use strict';

import Push from "push.js";

$(document).ready(function () {

    const es = new EventSource('http://localhost:3000/hub?topic=' + encodeURIComponent('http://example.com/books/1'));
    es.onmessage = e => {
        console.dir(JSON.parse(e.data));

        // Push.create("Hello world!", {
        //     body: "How's it hangin'?",
        //     icon: 'https://dev.core.crosier/build/static/images/favicon.ico',
        //     timeout: 1000,
        //     onClick: function () {
        //         window.focus();
        //         this.close();
        //     }
        // });

    }

});
Koi.directive('itemStock', function () {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {

            let table = document.getElementById('table-talles');
            $(document).ready(function () {
                //console.log(element[0])
                $('[data-toggle="popover"]').popover({html:true, trigger:'focus'});
                //$('[data-toggle="popover"]').popover({html:true, placement:'bottom'});
                //let popOvers = document.querySelectorAll('div[data-toggle="popover"]');
                //Array.from(popOvers).forEach(function(popOver, index) {
                $(element).click(function() {
                    let talles = JSON.parse(attrs.itemStock);
                    let tallas = talles.reduce(function(acum, {talle}){
                        return acum + '<th>' + talle + '</th>';
                    }, '');
                    tallas = '' + tallas;

                    let cantidades = talles.reduce(function(acum, {cantidad}){
                        return acum + '<td>' + cantidad + '</td>';
                    }, '');
                    cantidades = '' + cantidades;
                    //table.querySelector('thead').innerHTML = '<tr>' + tallas + '</tr>';
                    //table.querySelector('tbody').innerHTML = '<tr>' + cantidades + '</tr>';

                    let table = '<table class="table table-bordered" id="table-talles"><thead>'
                        + '<tr>' + tallas + '</tr></thead><tbody><tr>' + cantidades + '</tr></tbody></table>';
                    //element.setAttribute('data-content', table);
                    element[0].setAttribute('data-content', table);
                    //element[0].setAttribute('tabindex', 0);
                    //$(element).popover({html:true, placement:'bottom', content: table, trigger : 'manual'});
                    //$(element[0]).popover({html:true, placement:'bottom'})
                    //$(element[0]).popover({title: 'Hola', content: table, html: true, placement: "bottom"}); 
                    //$('#modal-sizes').modal('toggle');

                });

                element.click();
            });
        }
    };
});


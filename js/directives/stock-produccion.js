Koi.directive('stockProduccion', function () {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            $(document).ready(function () {

                var raw = attrs.stockProduccion;

                if (!raw || raw === 'undefined' || raw.indexOf('-') === -1) {
                    element[0].innerHTML = 0;
                    return false;
                }

                var articulo = raw.split('-');

                if (!articulo[0] || !articulo[1] || articulo[1] === 'undefined') {
                    element[0].innerHTML = 0;
                    return false;
                }

                fetch('/content/api/stock_produccion.php?articulo=' + encodeURIComponent(articulo[0]) + '&color=' + encodeURIComponent(articulo[1]))
                    .then(function(res) {
                        return res.text().then(function(t) {
                            try {
                                return t ? JSON.parse(t) : {};
                            } catch(e) {
                                console.error('JSON parse', e, t);
                                return {error: true};
                            }
                        });
                    })
                    .then(function(response) {
                        if (response.error) {
                            element[0].innerHTML = 0;
                            return false;
                        }

                        if (response.data && response.data.cantidad) {
                            element[0].innerHTML = response.data.cantidad;
                            return false;
                        }

                        element[0].innerHTML = 0;
                    })
                    .catch(function(error) {
                        console.error(error);
                        element[0].innerHTML = 0;
                    });

            });
        }
    };
});

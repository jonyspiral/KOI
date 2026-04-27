Koi.directive('stockProduccion', function () {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            $(document).ready(function () {

                let articulo = attrs.stockProduccion.split('-');
                //consulto la api del stock de produccion
                  fetch('/content/api/stock_produccion.php?articulo=' + articulo[0] + '&color=' + articulo[1])
                      .then(function(res) {
                          return res.json();
                      }).then(function(response){
                        if (response.error) {
                          element[0].innerHTML = 0;
                          return false;
                        }
                        if (response.data && response.data.cantidad) {
                         element[0].innerHTML = response.data.cantidad;
                         return false;
                        }

                      }).catch(function(error){
                          console.error(error);
                      });

            });
        }
    };
});


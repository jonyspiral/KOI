Koi.directive('stockProduccion', function () {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            $(document).ready(function () {

                let articulo = attrs.stockProduccion.split('-');
                //consulto la api del stock de produccion
                  fetch('/content/api/stock_produccion.php?articulo=' + articulo[0] + '&color=' + articulo[1])
                      .then(function(res) {
                          return res.text().then(function(t){try{return t?JSON.parse(t):{};}catch(e){console.error('JSON parse',e,t);return {error:true};}});
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


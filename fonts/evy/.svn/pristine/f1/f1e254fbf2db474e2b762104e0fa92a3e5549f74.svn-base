Koi.factory('ServiceCatalogo', ['$rootScope', '$http', function ($rootScope, $http) {

  var service = {
    basePath: '/content/cliente/',

    filtros: {
      show: false,
      aplicados: {},

      reiniciar: function () {
        this.aplicados = {
          tipoProductoStock: {}
        };
      },

      set: function (key, val) {
        this.aplicados[key] = val;
        $rootScope.$broadcast('Catalogo:FiltrosAplicados:changed', this.aplicados);
      }
    },

    actualizarFiltros: function (filtros, callback) {
      $http.post(this.basePath + 'actualizarFiltros.php', {filtros: filtros}).success(function (result) {
        if (funciones.getJSONType(result) !== funciones.jsonSuccess) {
          callback && callback(funciones.getJSONMsg(result));
        } else {
          callback && callback(null, result);
        }
      });
    }

  };

  service.filtros.reiniciar();

  return service;
}]);


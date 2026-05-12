Koi.factory('ServiceCliente', ['$http', function ($http) {
  var service = {
    basePath: '/content/cliente/',

    post: function (url, obj, callback) {
      $http.post(url, obj).success(function (result) {
        if (funciones.getJSONType(result) !== funciones.jsonSuccess) {
          callback(funciones.getJSONMsg(result));
        } else {
          callback(null, result);
        }
      });
    },

    addFavorito: function (articulo, callback) {
      this.post(
        this.basePath + 'favoritos/agregar.php',
        {idArticulo: articulo.idArticulo, idColor: articulo.idColorPorArticulo},
        callback
      );
    },

    removeFavorito: function (articulo, callback) {
      this.post(
        this.basePath + 'favoritos/borrar.php',
        {idArticulo: articulo.idArticulo, idColor: articulo.idColorPorArticulo},
        callback
      );
    },

    updateCurva: function (articulo, curva, callback) {
      this.post(
        this.basePath + 'favoritos/editarCurva.php',
        {idArticulo: articulo.idArticulo, idColor: articulo.idColorPorArticulo, idCurva: curva.id, unidades: curva.unidadesSeleccionadas},
        callback
      );
    },

    updateLibre: function (articulo, callback) {
      this.post(
        this.basePath + 'favoritos/editarLibre.php',
        {idArticulo: articulo.idArticulo, idColor: articulo.idColorPorArticulo, cantidades: articulo.paresLibres},
        callback
      );
    },

    confirmarPedido: function (datos, callback) {
      this.post(this.basePath + 'pedidos/agregar.php', datos, callback);
    },

    cancelarPedido: function (pedido, callback) {
      this.post(this.basePath + 'pedidos/borrar.php', {id: pedido.id}, callback);
    }
  };

  return service;
}]);


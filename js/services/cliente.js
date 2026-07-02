Koi.factory('ServiceCliente', ['$http', function ($http) {
  async function postFavoritosBatch(url, favoritos) {
    const res = await fetch(url, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ favorites: favoritos })
    });

    const raw = await res.text();
    if (!res.ok) {
      throw new Error(`HTTP ${res.status} - ${raw || 'sin cuerpo'}`);
    }

    try {
      return JSON.parse(raw);
    } catch (e) {
      throw new Error(raw || 'Respuesta JSON invalida');
    }
  }

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

    addFavoritoBatch: async function (favoritos) {
      return postFavoritosBatch('/content/cliente/favoritos/agregarVarios.php', favoritos);
    },

    removeFavorito: function (articulo, callback) {
      this.post(
        this.basePath + 'favoritos/borrar.php',
        {idArticulo: articulo.idArticulo, idColor: articulo.idColorPorArticulo},
        callback
      );
    },

    removeFavoritoBatch: async function (favoritos) {
      return postFavoritosBatch('/content/cliente/favoritos/borrarVarios.php', favoritos);
    },

    removeAllFavs: async function () {
      const res = await fetch('/content/cliente/favoritos/borrarTodos.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' }
      });

      const raw = await res.text();
      if (!res.ok) {
        throw new Error(`HTTP ${res.status} - ${raw || 'sin cuerpo'}`);
      }

      try {
        return JSON.parse(raw);
      } catch (e) {
        throw new Error(raw || 'Respuesta JSON invalida');
      }
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

    generarReportePedido: function (datos, callback) {
      this.post(this.basePath + 'pedidos/index.php', datos, callback);
    },

    cancelarPedido: function (pedido, callback) {
      this.post(this.basePath + 'pedidos/borrar.php', {id: pedido.id}, callback);
    }
  };

  return service;
}]);

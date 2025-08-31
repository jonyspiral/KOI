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
addFavoritoBatch: async function (favoritos) {
  console.log("📦 JSON ENVIADO:", JSON.stringify({ favorites: favoritos }));

  return await fetch('/content/cliente/favoritos/agregarVarios.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ favorites: favoritos })
  })
  .then(res => res.text())
  .then(texto => {
    console.log("🔴 Respuesta cruda del servidor:", texto);
    try {
      const json = JSON.parse(texto);
      console.log("✅ Respuesta parseada JSON:", json);
      return json;
    } catch (e) {
      console.error("❌ No se pudo parsear JSON:", e);
      throw e;
    }
  });
},



    removeFavorito: function (articulo, callback) {
      //console.log("articulo", articulo, "callback", callback);
      this.post(
        this.basePath + 'favoritos/borrar.php',
        {idArticulo: articulo.idArticulo, idColor: articulo.idColorPorArticulo},
        callback
      );
    },

    removeFavoritoBatch: async function (articulos) {
      /*const data = new FormData();
      data.append('idArticulo', articulo.idArticulo);
      data.append('idColor', articulo.idColorPorArticulo);*/
      let data = JSON.stringify( { favorites : articulos } )

      let res = await fetch(this.basePath + 'favoritos/borrarVarios.php', {
        method: 'POST',
        headers: { // cabeceras HTTP
            // vamos a enviar los datos en formato JSON
            'Content-Type': 'application/json'
        },
        body: data
      });
     
      res = res.json()

      return res;
      /*this.post(
        this.basePath + 'favoritos/borrarVarios.php',
        { favorites : articulos },
        callback
      );*/
    },

    removeAllFavs: async function () {

      let res = await fetch(this.basePath + 'favoritos/borrarTodos.php', {
        method: 'POST',
        headers: { // cabeceras HTTP
            // vamos a enviar los datos en formato JSON
            'Content-Type': 'application/json'
        },
      });
     
      res = res.json()

      return res;  
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


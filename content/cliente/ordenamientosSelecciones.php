
<style>
  .dropdown {
    position: relative;
    display: inline-block;
    /* margin-right: 15px; */
    margin-bottom: 10px;
  }

  .dropdown-toggle {
    font-size: 16px;
    cursor: pointer;
    padding: 8px 16px;
    border: 1px solid #bbb;
    border-radius: 6px;
    /* background-color: #fff; */
    transition: background-color 0.2s;
    user-select: none;
  }

  .dropdown-toggle:hover {
    background-color: #f0f0f0;
  }

  .dropdown-menu {
    position: absolute;
    top: 100%;
    left: 0;
    min-width: 200px;
    background-color: white;
    opacity: 75%;
    border: 1px solid #ccc;
    border-radius: 6px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.15);
    z-index: 1000;
    padding: 5px 0;
    display: none;
  }

  .dropdown-menu div {
    padding: 10px 16px;
    cursor: pointer;
    font-size: 15px;
  }

  .dropdown-menu div:hover {
    background-color: #f7f7f7;
  }

  .dropdown.open .dropdown-menu {
    display: block;
  }
</style>

<div class="orders-selections" id="catalogo-dropdowns">
  <div class="dropdown" ng-class="{open: sortMenuVisible}">
    <div class="dropdown-toggle" ng-click="sortMenuVisible = !sortMenuVisible; favoriteMenuVisible = false; $event.stopPropagation();">
      <i class="fa fa-sort"></i> Orden <i class="fa fa-caret-down"></i>
    </div>
    <div class="dropdown-menu" ng-click="$event.stopPropagation()">
      <div ng-click="applySortBy('catalogo')">Catalogo</div>
      <div ng-click="applySortBy('stock')">Stock</div>
      <div ng-click="applySortBy('category')">Categoria</div>
      <div ng-click="applySortBy('mayor-precio-mayorista')">Mayor precio (Mayorista)</div>
      <div ng-click="applySortBy('menor-precio-mayorista')">Menor precio (Mayorista)</div>
    </div>
  </div>

  <div class="dropdown" ng-class="{open: favoriteMenuVisible}">
    <div class="dropdown-toggle" ng-click="favoriteMenuVisible = !favoriteMenuVisible; sortMenuVisible = false; $event.stopPropagation();">
      <i class="fa fa-star"></i> Favorito <i class="fa fa-caret-down"></i>
    </div>
    <div class="dropdown-menu" ng-click="$event.stopPropagation()">
      <div ng-click="addFavoriteButton()">Seleccionar Todos</div>
      <div ng-click="removeFavoriteButton()">Sacar Todos</div>
    </div>
  </div>
</div>

<script>
  document.addEventListener('click', function (e) {
    var scope = angular.element(document.getElementById('catalogo-dropdowns')).scope();
    scope.$apply(function () {
      scope.sortMenuVisible = false;
      scope.favoriteMenuVisible = false;
    });
  });
</script>

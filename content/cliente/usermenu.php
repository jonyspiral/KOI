<script>
  $(document).ready(function () {
    $(document).click(function(event) {
      if (! $(event.target).closest('.navbar-user, .mobile-user-menu').length) {
        $('.navbar-user, .mobile-user-menu').removeClass('open');
      }
    })
  });
</script>

<ul class="user-menu nav navbar-nav navbar-right hidden-xs">
    <li class="navbar-company">
        <? echo Usuario::logueado()->contacto->nombre; ?>
    </li>
    <li>
        <a href="/favoritos/" class="navbar-favorites">
            <i class="fa fa-fw fa-star-o white fa-4x"></i>
        </a>
    </li>
    <li class="dropdown navbar-user">
        <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
            <i class="fa fa-fw fa-user fa-3x"></i>
            <b class="caret" style="margin-left: 0; margin-right: 3px; height: 20px;"></b>
        </a>
        <ul class="dropdown-menu animated fadeInDown">
            <li class="arrow"></li>
            <!--<li><a href="/perfil/"><i class="fa fa-fw fa-user"></i> <? echo Usuario::logueado()->contacto->nombre; ?></a></li>-->
            <li><a href="/favoritos/"><i class="fa fa-fw fa-star-half-empty fa-star"></i> Favoritos</a></li>
            <li><a href="/pedidos/"><i class="fa fa-fw fa-shopping-cart"></i> Mis pedidos</a></li>
            <li class="divider"></li>
            <li><a href="/logout/"><i class="fa fa-fw fa-sign-out"></i> Cerrar sesi�n</a></li>
        </ul>
    </li>
</ul>
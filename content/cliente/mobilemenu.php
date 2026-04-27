<div class="mobile-company-name user-menu visible-xs">
    <? echo Usuario::logueado()->contacto->nombre; ?>
</div>
<div class="mobile-menu visible-xs">
    <div class="row">
        <div class="col-xs-4">
            <a href="javascript:;" class="sidebar-button">
                <div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                    <div class="bar"></div>
                </div>
            </a>
        </div>
        <div class="col-xs-4">
            <a href="/favoritos/" class="navbar-favorites">
                <i class="fa fa-fw fa-star-o white fa-4x"></i>
            </a>
        </div>
        <div class="col-xs-4 mobile-user-menu dropdown">
            <a href="javascript:;" class="dropdown-toggle" data-toggle="dropdown">
                <i class="fa fa-fw fa-user fa-4x"></i>
            </a>
            <ul class="dropdown-menu animated fadeInUp" style="top: inherit; left: auto; right: 17px; bottom: 100%; margin-top: 1px;">
                <li><a href="/logout/"><i class="fa fa-fw fa-sign-out"></i> Cerrar sesión</a></li>
                <li class="divider"></li>
                <li><a href="/pedidos/"><i class="fa fa-fw fa-shopping-cart"></i> Mis pedidos</a></li>
                <li><a href="/favoritos/"><i class="fa fa-fw fa-star-half-empty fa-star"></i> Favoritos</a></li>
                <!--<li><a href="/perfil/"><i class="fa fa-fw fa-user"></i> <? echo Usuario::logueado()->contacto->nombre; ?></a></li>-->
                <li class="arrow"></li>
            </ul>
        </div>
    </div>
</div>

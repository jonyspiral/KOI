var menuKoi = {

arrowimages: {right: ['rightarrowclass', '/img/menu/right.gif']},
transition: {overtime: 100, outtime: 200}, //Duración del slideIn y slideOut
shadow: {enable: true, offsetx: 3, offsety: 3}, //Tamaño del shadow
showhidedelay: {showdelay: 200, hidedelay: 350}, // Delay del mouseOver/mouseOut antes de aparecer/desaparecer

detectwebkit: navigator.userAgent.toLowerCase().indexOf('applewebkit')!=-1, //Detecta browser WebKit (Safari, Chrome)
detectie6: document.all && !window.XMLHttpRequest,
css3support: window.msPerformance || (!document.all && document.querySelector), //Detecta browser con CSS3 shadows (IE9, FF3.5, Safari3, Chrome)

buildmenu: function($, setting){
	var varMenuKoi = menuKoi;
	var $mainmenu = $('#' + setting.mainmenuid + ' ul');
	$mainmenu.parent().get(0).className = setting.classname || 'menuKoi';
	var $headers = $mainmenu.find('ul').parent();
	$headers.hover(
		function(e){
			$(this).children('div:eq(0), a:eq(0)').addClass('selected');
		},
		function(e){
			$(this).children('div:eq(0), a:eq(0)').removeClass('selected');
		}
	);
	$headers.each(function(i){ //Recorre los LI headers
		var $curobj = $(this).css({zIndex: 1000 - i}); //A cada LI header le pongo un zIndex menor que el anterior
		var $subul = $(this).find('ul:eq(0)').css({display:'block'});
		$subul.data('timers', {});
		this._dimensions = {w: this.offsetWidth, h: this.offsetHeight, subulw: $subul.outerWidth(), subulh: $subul.outerHeight()};
		this.istopheader = ($curobj.parents('ul').length == 1 ? true : false); //Es el MainMenu?
		$subul.css({top: (this.istopheader ? this._dimensions.h + 'px' : 0)});
		$curobj.children('a:eq(0)').append( //Si no es MainMenu, agrego la Arrow Right
			(this.istopheader ? '' : 
				'<img src="' + varMenuKoi.arrowimages.right[1]
				+'" class="' + varMenuKoi.arrowimages.right[0]
				+ '" style="border:0;" />'
			)
		);
		if (varMenuKoi.shadow.enable && !varMenuKoi.css3support){ //Si el browser no soporta CSS3 shadows
			this._shadowoffset = {x: (this.istopheader ? $subul.offset().left + varMenuKoi.shadow.offsetx : this._dimensions.w), y: (this.istopheader ? $subul.offset().top + varMenuKoi.shadow.offsety : $curobj.position().top)}; //Calcula los offsets de shadow según config
			if (this.istopheader)
				$parentshadow = $(document.body);
			else{
				var $parentLi = $curobj.parents('li:eq(0)');
				$parentshadow = $parentLi.get(0).$shadow;
			}
			this.$shadow = $('<div class="ddshadow' + (this.istopheader ? ' toplevelshadow' : '') + '"></div>').prependTo($parentshadow).css({left: this._shadowoffset.x + 'px', top: this._shadowoffset.y + 'px'});  //Agrega un DIV de shadow y lo agrega como padre del próximo DIV con shadow
		}
		$curobj.click(
			function(e){
				var $targetul = $subul; //UL a mostrar
				var header = $curobj.get(0);
				clearTimeout($targetul.data('timers').hidetimer);
				$targetul.data('timers').showtimer = setTimeout(function(){
					header._offsets = {left: $curobj.offset().left, top: $curobj.offset().top};
					var menuleft = header.istopheader ? 0 : header._dimensions.w;
					menuleft = (header._offsets.left + menuleft + header._dimensions.subulw > $(window).width()) ? (header.istopheader ? - header._dimensions.subulw + header._dimensions.w : - header._dimensions.w) : menuleft; //Calcula la distancia de este SubMenu al padre
					if ($targetul.queue().length <= 1){ //Si hay 1 o más animaciones esperando en cola
						$targetul.css({left: menuleft + 'px', width: header._dimensions.subulw + 'px'}).animate({height: 'show', opacity: 'show'}, menuKoi.transition.overtime);
						if (varMenuKoi.shadow.enable && !varMenuKoi.css3support){
							var shadowleft = header.istopheader ? $targetul.offset().left + menuKoi.shadow.offsetx : menuleft;
							var shadowtop = header.istopheader ? $targetul.offset().top + varMenuKoi.shadow.offsety : header._shadowoffset.y;
							if (!header.istopheader && menuKoi.detectwebkit){ //Si el browser es WebKit, vuelve el shadow opacity a 1
								header.$shadow.css({opacity: 1});
							}
							header.$shadow.css({overflow: '', width: header._dimensions.subulw + 'px', left: shadowleft + 'px', top: shadowtop + 'px'}).animate({height: header._dimensions.subulh + 'px'}, menuKoi.transition.overtime);
						}
					}
				}, menuKoi.showhidedelay.showdelay);
			}
		);
		$curobj.mouseleave(
			function(e){
				var $targetul = $subul;
				var header = $curobj.get(0);
				clearTimeout($targetul.data('timers').showtimer);
				$targetul.data('timers').hidetimer = setTimeout(function(){
					$targetul.animate({height: 'hide', opacity: 'hide'}, menuKoi.transition.outtime);
					if (varMenuKoi.shadow.enable && !varMenuKoi.css3support){
						if (menuKoi.detectwebkit){ //Si el browser es WebKit, pone el shadow opacity del primer hijo a 0 (pq no anda 'overflow:hidden')
							header.$shadow.children('div:eq(0)').css({opacity: 0});
						}
						header.$shadow.css({overflow: 'hidden'}).animate({height: 0}, menuKoi.transition.outtime);
					}
				}, menuKoi.showhidedelay.hidedelay);
			}
		);
		$curobj.hover(
			function(e){
				var $targetul = $subul; //UL a mostrar
				var header = $curobj.get(0);
				clearTimeout($targetul.data('timers').hidetimer);
				$targetul.data('timers').showtimer = setTimeout(function(){
					header._offsets = {left: $curobj.offset().left, top: $curobj.offset().top};
					var menuleft = header.istopheader ? 0 : header._dimensions.w;
					menuleft = (header._offsets.left + menuleft + header._dimensions.subulw > $(window).width()) ? (header.istopheader ? - header._dimensions.subulw + header._dimensions.w : - header._dimensions.w) : menuleft; //Calcula la distancia de este SubMenu al padre
					if ($targetul.queue().length <= 1){ //Si hay 1 o más animaciones esperando en cola
						$targetul.css({left:menuleft + 'px', width: header._dimensions.subulw + 'px'}).animate({height: 'show', opacity: 'show'}, menuKoi.transition.overtime);
						if (varMenuKoi.shadow.enable && !varMenuKoi.css3support){
							var shadowleft = header.istopheader ? $targetul.offset().left + menuKoi.shadow.offsetx : menuleft;
							var shadowtop = header.istopheader ? $targetul.offset().top + varMenuKoi.shadow.offsety : header._shadowoffset.y;
							if (!header.istopheader && menuKoi.detectwebkit){ //Si el browser es WebKit, vuelve el shadow opacity a 1
								header.$shadow.css({opacity: 1});
							}
							header.$shadow.css({overflow: '', width: header._dimensions.subulw + 'px', left: shadowleft + 'px', top:shadowtop + 'px'}).animate({height:header._dimensions.subulh+'px'}, menuKoi.transition.overtime);
						}
					}
				}, menuKoi.showhidedelay.showdelay);
			},
			function(e){
				var $targetul = $subul;
				var header = $curobj.get(0);
				clearTimeout($targetul.data('timers').showtimer);
				$targetul.data('timers').hidetimer = setTimeout(function(){
					$targetul.animate({height: 'hide', opacity: 'hide'}, menuKoi.transition.outtime);
					if (varMenuKoi.shadow.enable && !varMenuKoi.css3support){
						if (menuKoi.detectwebkit){ //Si el browser es WebKit, pone el shadow opacity del primer hijo a 0 (pq no anda 'overflow:hidden')
							header.$shadow.children('div:eq(0)').css({opacity: 0});
						}
						header.$shadow.css({overflow: 'hidden'}).animate({height: 0}, menuKoi.transition.outtime);
					}
				}, menuKoi.showhidedelay.hidedelay);
			}
		);
	});
	if (varMenuKoi.shadow.enable && varMenuKoi.css3support){ //Si el browser no soporta CSS3 shadows
		var $toplevelul = $('#' + setting.mainmenuid + ' ul li ul');
		var css3shadow = parseInt(varMenuKoi.shadow.offsetx) + 'px ' + parseInt(varMenuKoi.shadow.offsety) + 'px 5px #aaa'; //Valor de CSS3 box-shadow
		var shadowprop = ['boxShadow', 'MozBoxShadow', 'WebkitBoxShadow', 'MsBoxShadow']; //Nombre de las posibles propiedades CSS3 para shadow
		for (var i = 0; i < shadowprop.length; i++){
			$toplevelul.css(shadowprop[i], css3shadow);
		}
	}
	$mainmenu.find('ul').css({display: 'none', visibility: 'visible'});
},

init:function(setting){
	this.shadow.enable = (document.all && !window.XMLHttpRequest) ? false : this.shadow.enable; //Si es IE6, deshabilitar shadows
	jQuery(document).ready(function($){
		menuKoi.buildmenu($, setting);
	});
}

};

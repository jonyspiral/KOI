(function($) {
	$.fn.checktree = function(options) {
		var defaults = {
			checkParents : true, // When checking a box, all parents are checked
			checkChildren : true, // When checking a box, all children are checked
			uncheckChildren : true, // When unchecking a box, all children are unchecked
			initialState : 'collapse' // Options - 'expand' (fully expanded), 'collapse' (fully collapsed) or default
		};
		var options = $.extend(defaults, options);
		this.each(function() {
			var $root = this;
			// Agrego los + y -
			$(this).find('li').prepend('<span class="treeControl">&nbsp;</span>');
			$(this).find("li:has(> ul:not(.hide)) > span.treeControl").addClass('expanded').html('-');
			$("li:has(> ul.hide) > span.treeControl", $(this)).addClass('collapsed').html('+');
			// Checkbox click function
			$("input[type='checkbox']", $(this)).click(function(){
				if ($(this).is(":checked")) {
					$("> ul", $(this).parent("li")).removeClass('hide');
					$("> span.collapsed", $(this).parent("li")).removeClass("collapsed").addClass("expanded").html('-');
					if (defaults.checkParents) {
						$(this).parents('li').find("input[type='checkbox']:first").attr('checked', true);
					}
					if (defaults.checkChildren) {
						var p = $(this).parents('li:first');
						p.find('input[type="checkbox"]').attr('checked', true);
						p.find('ul').removeClass('hide');
						p.find('span.collapsed').removeClass('collapsed').addClass('expanded').html('-');
					}
				} else {
					if (defaults.uncheckChildren) {
						var p = $(this).parents('li:first');
						p.find("input[type='checkbox']").attr('checked', false);
						p.find('ul').addClass('hide');
						p.find('span.expanded').removeClass('expanded').addClass('collapsed').html('+');
					}
				}
			});
			// Text click function
			$(this).find("li:has(> ul) span.treeControl").click(function(){
				if ($(this).is(".collapsed")) {
					$("> ul", $(this).parent("li")).removeClass('hide');
					$(this).removeClass("collapsed").addClass("expanded").html('-');
				} else if ($(this).is(".expanded")) {
					$("> ul", $(this).parent("li")).addClass('hide');
					$(this).removeClass("expanded").addClass("collapsed").html('+');
				}
			});
			switch(defaults.initialState) {
				case 'expand':
					$("ul", $root).removeClass('hide');
					$("li:has(> ul) > span.treeControl", $root).removeClass("collapsed").addClass("expanded").html('-');
					return false;
					break;
				case 'collapse':
					$("ul", $root).addClass('hide');
					$("li:has(> ul) > span.treeControl", $root).removeClass("expanded").addClass("collapsed").html('+');
					return false;
					break;
			}
		});
		return this;
	};
	
})(jQuery);

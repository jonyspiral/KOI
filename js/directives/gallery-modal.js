Koi.directive('galleryModal', function () {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            $(document).ready(function () {
            	var images2 = JSON.parse(attrs.galleryModal);

				var imgFiltered = []
	        	images2.forEach(function(image) {
	        		let img = document.createElement('img');
	        		img.src = image.ruta;
	        		img.onload = function() {
	        			imgFiltered.push({url: image.ruta, order:image.orden})
	        		}
	        	});
                $(element).click(function () {
                    $('#carousel-gallery').carousel();
                    $('#carousel-gallery').carousel(0);
                    //let images = JSON.parse(attrs.galleryModal);
                    
                    let galleryModal = document.querySelector('#gallery-modal');
                    let carouselInner = galleryModal.querySelector('div.carousel-inner');
                    let indicators = galleryModal.querySelector('ol.carousel-indicators');
                    indicators.innerHTML = '';
                    carouselInner.innerHTML = '';

                    /*images = images.forEach(function(url, i) {
                        if (i == 0) {
                            indicators.innerHTML += `<li data-target="#carousel-gallery" data-slide-to="0" class="active"></li>`;
                            carouselInner.innerHTML += `<div class="item active"><img src="${url}" data-holder-rendered="true"></div>`;
                        } else {
                            indicators.innerHTML += `<li data-target="#carousel-gallery" data-slide-to="${i}" class=""></li>`;
                            carouselInner.innerHTML += `<div class="item"><img src="${url}" data-holder-rendered="true"></div>`;
                        }
                    });*/
                    imgFiltered.sort(function (a, b) {
                      if (a.order > b.order) { return 1; }
                      if (a.order < b.order) { return -1; }
                      return 0;
                    });
					imgFiltered.forEach(function(img, i) {
                        if (i == 0) {
                            indicators.innerHTML += `<li data-target="#carousel-gallery" data-slide-to="0" class="active"></li>`;
                            carouselInner.innerHTML += `<div class="item active"><img src="${img.url}" data-holder-rendered="true"></div>`;
                        } else {
                            indicators.innerHTML += `<li data-target="#carousel-gallery" data-slide-to="${i}" class=""></li>`;
                            carouselInner.innerHTML += `<div class="item"><img src="${img.url}" data-holder-rendered="true"></div>`;
                        }
                    });


                    $('#gallery-modal').modal('toggle');
                    document.querySelector('#carousel-gallery-prev').addEventListener('click', function(){
                        $('#carousel-gallery').carousel('prev');
                    });
                    document.querySelector('#carousel-gallery-next').addEventListener('click', function(){
                        $('#carousel-gallery').carousel('next');
                    });

                });
            });
        }
    };
});


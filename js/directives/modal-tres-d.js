Koi.directive('modalTresD', function () {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            $(document).ready(function () {
                let divIframe = document.querySelector('#' + attrs.modalTresD);
                let rutaIframe = divIframe.querySelector('._ruta_iframe_3d_');
                let html3D = `<h5>No hay imagen 3d</h5>`;
                if (rutaIframe.innerText.trim().length > 0) {
                    html3D = `<iframe src="${rutaIframe.innerText}" width="500"  height="489"  frameborder="0"  scrolling="no"  ></iframe>`;
                }
                divIframe.querySelector('.modal-body').innerHTML = html3D;
                $(element).click(function () {
                    $('#' + attrs.modalTresD).modal('toggle');
                });
            });
        }
    };
});


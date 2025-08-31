Koi.directive('modal3D', function () {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            $(document).ready(function () {
              $(element).click(function () {
                var trigger = $(this);
                document.querySelector('#modal-iframe-3d').innerHTML = attrs.modal3D;
                $('#3d-modal').show();
              });
            });
        }
    };
});


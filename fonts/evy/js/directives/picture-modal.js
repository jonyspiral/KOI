Koi.directive('pictureModal', function () {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {
            $(document).ready(function () {
              $(element).click(function () {
                var trigger = $(this);
                $('#modal-image').attr('src', trigger.find('img').attr('src'));
                $('#modal-caption').text(trigger.parent().text().trim());
                $('#page-modal').show();
              });
            });
        }
    };
});


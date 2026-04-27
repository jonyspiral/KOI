Koi.directive('sidebar', function () {
    return {
        restrict: 'A',
        link: function (scope, element, attrs) {

            $(document).ready(function () {

                /* 01. Handle Scrollbar
                 ------------------------------------------------ */
                var generateSlimScroll = function (element) {
                    var dataHeight = $(element).attr('data-height');
                    dataHeight = (!dataHeight) ? $(element).height() : dataHeight;

                    var scrollBarOption = {
                        height: dataHeight,
                        alwaysVisible: true
                    };
                    if (/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                        $(element).css('height', dataHeight);
                        $(element).css('overflow-x', 'scroll');
                    } else {
                        $(element).slimScroll(scrollBarOption);
                    }
                };

                'use strict';
                $('[data-scrollbar=true]').each(function () {
                    generateSlimScroll($(this));
                });

                /* 02. Handle Sidebar - Menu
                 ------------------------------------------------ */
                'use strict';
                $('.sidebar .nav > .has-sub > a').click(function () {
                    var target = $(this).next('.sub-menu');
                    var otherMenu = '.sidebar .nav > li.has-sub > .sub-menu';

                    if ($('.page-sidebar-minified').length === 0) {
                        $(otherMenu).not(target).slideUp(250, function () {
                            $(this).closest('li').removeClass('expand');
                        });
                        $(target).slideToggle(250, function () {
                            var targetLi = $(this).closest('li');
                            if ($(targetLi).hasClass('expand')) {
                                $(targetLi).removeClass('expand');
                            } else {
                                $(targetLi).addClass('expand');
                            }
                        });
                    }
                });
                $('.sidebar .nav > .has-sub .sub-menu li.has-sub > a').click(function () {
                    if ($('.page-sidebar-minified').length === 0) {
                        var target = $(this).next('.sub-menu');
                        $(target).slideToggle(250);
                    }
                });

                /* 03. Handle Sidebar - Mobile View Toggle
                 ------------------------------------------------ */
                var sidebarProgress = false;
                $('.sidebar').on('click touchstart', function (e) {
                    if ($(e.target).closest('.sidebar').length !== 0) {
                        sidebarProgress = true;
                    } else {
                        sidebarProgress = false;
                        e.stopPropagation();
                    }
                });

                $(document).on('click touchstart', function (e) {
                    if ($(e.target).closest('.sidebar').length === 0) {
                        sidebarProgress = false;
                    }

                    if (!e.isPropagationStopped() && sidebarProgress !== true) {
                        if ($('#page-container').hasClass('page-sidebar-toggled')) {
                            //sidebarProgress = true;
                            $('#page-container').removeClass('page-sidebar-toggled');
                        }
                    }
                });

                $('[data-click=sidebar-toggled]').click(function (e) {
                    e.stopPropagation();
                    var sidebarClass = 'page-sidebar-toggled';
                    var sidebarStickyClass = 'page-sidebar-sticky';
                    var targetContainer = '#page-container';

                    if ($(window).width() < 480) {
                        $(targetContainer).removeClass(sidebarStickyClass);
                    }
                    if ($(targetContainer).hasClass(sidebarClass) || $(targetContainer).hasClass(sidebarStickyClass)) {
                        $(targetContainer).removeClass(sidebarClass).removeClass(sidebarStickyClass);
                    } else if (sidebarProgress !== true) {
                        $(targetContainer).addClass(sidebarClass);
                        if ($(window).width() > 480) {
                            $(targetContainer).addClass(sidebarStickyClass);
                        }
                    } else {
                        sidebarProgress = false;
                    }
                });

                /* 04. Handle Sidebar - Minify / Expand
                 ------------------------------------------------ */
                $('[data-click=sidebar-minify]').click(function (e) {
                    e.preventDefault();
                    var sidebarClass = 'page-sidebar-minified';
                    var targetContainer = '#page-container';
                    if ($(targetContainer).hasClass(sidebarClass)) {
                        $(targetContainer).removeClass(sidebarClass);
                        if ($(targetContainer).hasClass('page-sidebar-fixed')) {
                            generateSlimScroll($('#sidebar [data-scrollbar="true"]'));
                            $('#sidebar [data-scrollbar=true]').trigger('mouseover');
                            $('#sidebar [data-scrollbar=true]').stop();
                            $('#sidebar [data-scrollbar=true]').css('margin-top', '0');
                        }
                    } else {
                        $(targetContainer).addClass(sidebarClass);

                        if (!/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent)) {
                            if ($(targetContainer).hasClass('page-sidebar-fixed')) {
                                $('#sidebar [data-scrollbar="true"]').slimScroll({destroy: true});
                                $('#sidebar [data-scrollbar="true"]').removeAttr('style');
                            }
                            $('#sidebar [data-scrollbar=true]').trigger('mouseover');
                        } else {
                            $('#sidebar [data-scrollbar="true"]').css('margin-top', '0');
                            $('#sidebar [data-scrollbar="true"]').css('overflow', 'visible');
                        }
                    }
                    $(window).trigger('resize');
                });

                var userAgent = window.navigator.userAgent;
                var msie = userAgent.indexOf('MSIE ');

                if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./)) {
                    $('.vertical-box-row [data-scrollbar="true"][data-height="100%"]').each(function () {
                        var targetRow = $(this).closest('.vertical-box-row');
                        var targetHeight = $(targetRow).height();
                        $(targetRow).find('.vertical-box-cell').height(targetHeight);
                    });
                }
            });
        }
    };
});


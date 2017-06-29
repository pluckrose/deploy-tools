;(function ($) {
        var getTallestElement = function (elems) {
            var heights = elems.map(function () {
                return parseInt($(this).css("height"), 10);
            }).get();

            return Math.max.apply(Math, heights);
        },

        setHeights = function (elems) {
            var height = getTallestElement(elems);

            if ( $(window).width() <= 480 ) {
                elems.removeProp('style');
            }
            else {
                elems.removeProp('style').css('min-height', height);
            }
        },

        equalHeights = function () {
            $(".nosto-grid").each(function () {
                var self = $(this),
                    elems = self.find("[data-equal-height]");

                if (elems) {
                    setHeights(elems);
                }
            });

        };

        var $resizeEnd;

        $(window).on('resize.equalHeights', function() {
            clearTimeout($resizeEnd);

            $resizeEnd = setTimeout( function() {
                equalHeights();
            }, 150);

        });

        setTimeout( function() {
            equalHeights();
        }, 2000);
})(jQuery);
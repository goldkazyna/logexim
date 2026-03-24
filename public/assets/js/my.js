$(document).ready(function () {

    var swiperMain = new Swiper('.swiper-main', {
        slidesPerView: 1,
        spaceBetween: 10,
        loop: true,
        speed: 1200,
        easing: 'linear',
        autoplay: true,
        pagination: {
            el: '.main-pagination',
            type: 'bullets',
            clickable: true,
        },
        navigation: {
            nextEl: '.main-next',
            prevEl: '.main-prev',
        },
    });

    let products = document.querySelectorAll('.products-inner');

    products.forEach(function (section, sectionIndex) {

        let sliderMultiply = section.getElementsByClassName('swiper-multiply');
        let prev = section.getElementsByClassName('multiply-prev');
        let next = section.getElementsByClassName('multiply-next');
        let pagination = section.getElementsByClassName('pagination-multiply');

        new Swiper(sliderMultiply[0], {
            spaceBetween: 10,
            loop: true,

            breakpoints: {
                0: {
                    slidesPerView: 1,
                },
                576: {
                    slidesPerView: 2,
                },

                992: {
                    slidesPerView: 3,
                },
                1200: {
                    slidesPerView: 4,
                },
            },

            navigation: {
                nextEl: next[0],
                prevEl: prev[0],
                clickable: true,
            },
            pagination: {
                el: pagination[0],
                type: 'bullets',
                clickable: true,
            },

        });
    });


    //меню-слайдер на мобиле
    $('.sliding-menu').each(function () {
        var it = $(this),
            back = it.find('.back-btn'),
            slideInner = it.find('.sliding-inner'),
            dropLink = it.find('.drop-link > a'),
            counter = 0,
            times = 0.3;

        slideInner.css({
            transition: times + 's'
        });

        $('.sliding-menu').on('click', '.drop-link > a', function (e) {
            e.preventDefault();

            counter = ++counter;

            let dropMenu = $(this).siblings('.drop-menu');

            if (counter > 0) {
                back.addClass('active');
                slideInner.css({
                    transform: 'translateX(-' + counter + '00%)'
                });
            }

            dropMenu.show().addClass('open');

        });

        $(document).on('click', '.back-btn', function (e) {
            e.preventDefault();

            counter = --counter;

            if (counter < 1) {
                back.removeClass('active');
                slideInner.css({
                    transform: 'translateX(0)'
                });
            } else {
                slideInner.css({
                    transform: 'translateX(-' + counter + '00%)'
                });
            }
            setTimeout(function () {
                it.find('.drop-menu.open').eq(counter).hide().removeClass('open');
            }, times + 100);

        });

    });

    //откытие\закрытие меню
    $('.humb').click(function () {
        $('.mobile-menu, .humb, .menu-back').toggleClass('active');
    });
    $('.menu-back').click(function () {
        $(this).removeClass('active')
        $('.mobile-menu, .humb').removeClass('active');
    });

    //-----БОЛЬШЕ-МЕНЬШЕ-----//
    $('.minus').click(function () {
        var $input = $(this).parent().find('input');
        var count = parseInt($input.val()) - 1;
        count = count < 1 ? 1 : count;
        $input.val(count);
        $input.change();
        return false;
    });
    $('.plus').click(function () {
        var $input = $(this).parent().find('input');
        $input.val(parseInt($input.val()) + 1);
        $input.change();
        return false;
    });
});


//перемещение блоков
$(window).on('load resize', function () {
    appendBlocks('.main-menu-move', 0, 1199, '.mobile-menu .sliding-inner');
    appendBlocks('.main-menu-move', 1199, 0, '.main-menu');

    appendBlocks('.work-time-move', 0, 1199, '.for-other');
    appendBlocks('.work-time-move', 1199, 0, '.for-work-time');

    appendBlocks('.contacts-block-move', 0, 1199, '.for-other');
    appendBlocks('.contacts-block-move', 1199, 0, '.for-block-move');

    appendBlocks('.cart-move', 0, 1199, '.manage');
    appendBlocks('.cart-move', 1199, 0, '.for-cart');
});
 
function appendBlocks(block, windowMin, windowMax, appendTo) {
    var exists = $(appendTo).find(block)

    if (!exists.length) {
        if (windowMax == 0) {
            if ($(window).width() > windowMin) {
                $(block).appendTo($(appendTo));
            }
        } else {
            if ($(window).width() > windowMin && $(window).width() < windowMax) {
                $(block).appendTo($(appendTo));
            }
        }
    }
}

$(window).on('load', function () {
    if ($(window).width() > 1199 && $('.h-bottom').length) {
        menuFixed('.h-bottom', 0);
    }

    if ($(window).width() > 1199) {

        var offsetLefts = $('.header .cart').offset().left;

        var headerHeight = $('.header').outerHeight();
        var menuHeight = $('.h-bottom').outerHeight();

        menuFixed('.header .h-bottom', 0);
        menuFixed('.header .cart', headerHeight);

        $(window).scroll(function () {
            if ($('.header .h-bottom').hasClass('active')) {

                $('.header .cart').css({
                    top: menuHeight,
                    left: offsetLefts
                });
            } else {
                $('.header .cart').css({
                    top: 'initial',
                    left: 'initial'
                });
            }

        });
    }
});

function menuFixed(menu, offseting) {

    var height = $(menu).outerHeight(),
        offsetParametr = offseting,
        offsetTop = $(menu).offset().top + offsetParametr,

        wrapper = menu.replace(/[\s.#]/g, '') + '-wrapper'; // удаляем точку и хештег

    $(menu).wrap('<div class="' + wrapper + '"></div>');
    $('.' + wrapper).css({
        minHeight: height
    });

    $(window).scroll(function () {
        if ($(window).scrollTop() >= offsetTop) {
            $(menu).addClass('active');
        } else {
            $(menu).removeClass('active');
        }
    });
}
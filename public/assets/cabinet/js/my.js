$(document).ready(function () {

    //выпадающее меню бутстрап
    if (jQuery(window).width() > '767') {
        $('.dropdown > a').hover(
            function () {
                //show its submenu
                $(this).parent().children('.dropdown-menu').stop().fadeIn(300);
                $(this).parent().addClass('open');
            }
        );
        $('.dropdown').hover(null,
            function (e) {
                //hide its submenu
                $(this).children('.dropdown-menu').stop().fadeOut(100);
                $(this).removeClass('open');
            }
        );
    }

    $('.tech-humb').click(function () {
        $('.tech-mobile-menu, .tech-humb, .tech-menu-back').toggleClass('active');
    });
    $('.tech-menu-back').click(function () {
        $(this).removeClass('active')
        $('.tech-mobile-menu, .tech-humb').removeClass('active');
    });

    $('.tmb-row .inputblock').each(function () {
        var it = $(this),
            input = it.find('input'),
            icon = it.find('.icon');
        
        it.append('<p>');
        
        var p = it.find('p');
        
        p.text(input.val());
        
        input.on('input', function(){
           p.text($(this).val());
        });
        
        icon.click(function(){
            var inputblock = $(this).parent(),
                input = inputblock.find('input'),
                attr = input.attr('required');
                
            
           if(!inputblock.hasClass('active')) {
               inputblock.addClass('active'); 
           }
            else {
                if(!attr) {
                    inputblock.removeClass('active error');
                }
                if(input.val() != '') {
                    inputblock.removeClass('active error');
                }
                else if(attr){
                    inputblock.addClass('error');
                }
            }
        });
    });

});

$(window).on('load resize', function () {
    if ($(window).width() > 991) {
        var height = $('.tech-middle .for-title').outerHeight();
        $('.tech-right').css({
            paddingTop: height + 'px'
        });
    }

    appendBlocks('.tech-header .menu', 0, 1199, '.tech-mobile-menu .for-menu');
    appendBlocks('.tech-mobile-menu .for-menu .menu', 1200, 0, '.tech-header .for-menu');

    appendBlocks('.tech-header .login', 0, 1199, '.tech-mobile-menu .for-other');
    appendBlocks('.tech-mobile-menu .for-other .login', 1200, 0, '.tech-header .for-login');

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

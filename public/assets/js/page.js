$(document).ready(function() {
    $('.button-group__btn_minus').click(function(e) {
        e.preventDefault();
        var $input = $(this).siblings('.button-group__input');
        var value = parseInt($input.val());
        if (value > 1) {
            $input.val(value - 1);
        }
    });

    $('.button-group__btn_plus').click(function(e) {
        e.preventDefault();
        var $input = $(this).siblings('.button-group__input');
        var value = parseInt($input.val());
        $input.val(value + 1);
    });
});
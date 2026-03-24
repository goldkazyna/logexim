$(document).ready(function() {

    const mobileWidth = 1100;
    const btnBurger = $(".btn-burger");

    // Запуск (тестовый) функции определения статуса доставки. Эту функцию вызывать во время ajax ответа
    showStatusDelivery(80);

    Fancybox.bind('[data-fancybox]', {});

    // Маски для инпутов
    $.mask.definitions['9'] = '';
    $.mask.definitions['d'] = '[0-9]';

    $('[data-mask="phone"]').mask("+7 (ddd) dd-dd-ddd");

    $('.nav-modal__item').on('click', '.nav-modal__toggle', function (e){
        e.preventDefault();
        e.stopPropagation();
        $(this).toggleClass('_active');
        $(this).closest('.nav-modal__item').find('.nav-modal__submenu').slideToggle();
    });

    var animateButton = function(e) {
        e.preventDefault();

        //reset animation
        $(e.target).removeClass('animate');

        $(e.target).addClass('animate');
        setTimeout(function(){
            $(e.target).removeClass('animate');
        }, 700);
    };

    $('.btn_anim').on('mouseenter', animateButton);

    // Плавный скролл при клике на .hero__scroll
    $('.hero__scroll').on('click', function(e) {
        e.preventDefault();

        $('html, body').animate({
            scrollTop: $(window).height() - 50
        }, 1000);
    });

    // Проверка при загрузке страницы
    checkScroll();

    // Проверка при прокрутке страницы
    $(window).on('scroll', function() {
        checkScroll();
    });

    // Мобильное меню
    btnBurger.on("click", function (e){
        e.preventDefault();
        $('body').toggleClass('_modal-open');
    });


    // Меняем местами населенные пункты
    $('.swap').on('click', function(e) {
        e.preventDefault();
        // Находим поля ввода по атрибутам name
        var $inputFrom = $(this).parents('.form__swap').find('input[name="package_from"]');
        var $inputTo = $(this).parents('.form__swap').find('input[name="package_to"]');

        // Получаем текущие значения
        var fromValue = $inputFrom.val();
        var toValue = $inputTo.val();

        // Меняем значения местами
        $inputFrom.val(toValue);
        $inputTo.val(fromValue);
    });


    var swiperReviews = new Swiper('#sliderReviews', {
        slidesPerView: 3,
        loop: true,
        speed: 10000,
        freeModeMomentum: false,
        freeMode: true,
        autoplay: {
            delay: 1,
            disableOnInteraction: true
        },
        breakpoints: {
            320: {
                spaceBetween: 10,
                slidesPerView: 1.5,
            },
            520: {
                spaceBetween: 10,
                slidesPerView: 2,
            },
            880: {
                spaceBetween: 20,
                slidesPerView: 2.2,
            },
            1100: {
                spaceBetween: 30
            },
            1280: {
                spaceBetween: 40
            }
        }
    });

    document.querySelector('#sliderReviews').addEventListener('mouseenter', function (event) {
        if (event.target.classList.contains('swiper-slide')) {
            swiperReviews.autoplay.stop();
        }
    }, true);

    document.querySelector('#sliderReviews').addEventListener('mouseleave', function (event) {
        if (event.target.classList.contains('swiper-slide')) {
            swiperReviews.autoplay.start();
        }
    }, true);

    // Функция для обрезки текста
    function truncateText(text, maxWords) {
        var words = text.split(' ');
        if (words.length > maxWords) {
            return words.slice(0, maxWords).join(' ') + '...';
        }
        return text;
    }

    // Обработка всех отзывов
    $('.swiper-slide.review').each(function() {
        var $this = $(this);
        var $fullText = $this.find('.review__full');
        var $shortText = $this.find('.review__text');
        var $moreButton = $this.find('.review__more');
        var maxLength = parseInt($fullText.data('length'), 10);

        var fullTextContent = $fullText.text();
        var truncatedText = truncateText(fullTextContent, maxLength);

        $shortText.text(truncatedText);

        if (fullTextContent.split(' ').length > maxLength) {
            $moreButton.show(); // Показать кнопку, если текст больше максимальной длины
        } else {
            $moreButton.hide(); // Скрыть кнопку, если текст укладывается в лимит
        }
    });

    // Обработка клика на кнопку "Читать отзыв"
    $(document).on('click', '.review__more', function(e) {
        e.preventDefault();
        e.stopPropagation(); // Предотвращаем всплытие события

        var fullText = $(this).parents('.review ').find('.review__full').text();
        var metaText = $(this).parents('.review ').find('.review__about').text();

        // Обновляем содержимое модального окна
        $('#modalReview .modal__text').text(fullText);
        $('#modalReview .modal__title span').text(metaText);

        // Открываем модальное окно с Fancybox
        Fancybox.show([{ src: "#modalReview", type: "inline" }]);
    });

    function initAccordion(parentSelector, closeOthers) {
        const $parent = $(parentSelector);

        // Изначально скрываем ответы
        $parent.find('.faq__answer').not('._open').hide();

        // Обрабатываем клик по вопросу
        $parent.on('click', '.faq__question', function() {
            const $question = $(this);
            const $answer = $question.next('.faq__answer');
            const $parentItem = $question.closest('.faq__item');

            if (closeOthers) {
                // Закрываем другие ответы, если нужно закрывать остальные
                $parent.find('.faq__answer').not($answer).slideUp('fast').removeClass('_open');
                $parent.find('.faq__item').not($parentItem).removeClass('_open');
            }

            // Переключаем текущее состояние ответа
            $answer.slideToggle('fast');
            $parentItem.toggleClass('_open');
        });
    }

    // Инициализация аккордеона
    initAccordion('.faq__wrapper', true);

});


// Функция для проверки прокрутки и добавления/удаления класса _sticky-menu
function checkScroll() {
    if ($(window).scrollTop() >= $(window).height()) {
        $('body').addClass('_sticky-menu');
    } else {
        $('body').removeClass('_sticky-menu');
    }
}

function displayMainMenu(btn, display) {
    const menuMain = $(".nav-main");

}

// Функция обновлени позиции на таймлай
function showStatusDelivery( position ) {
    if ( $(window).width() > 680  ) {
        var container = $('.search-status'),
            currentLine = container.find('.search-status__active'),
            currentLabel = container.find('.search-status__current');

        currentLine.css({
            'width' : position + '%'
        })
        currentLabel.css({
            'left' : position + '%'
        })
    }
}
$('.nav__toggler').on('click', function () {
    $(this).closest('nav').toggleClass('open');
    $('.nav__items').toggleClass('displayed');
    $('body').toggleClass('overflow-hidden');
});

let lastScrollTop = curScrollTop = 0;
$(window).on('scroll', function () {
    curScrollTop = $(window).scrollTop();
    if (curScrollTop < lastScrollTop) {
        // Scroll vers le haut
        if (lastScrollTop <= $(window).height() - 50 && !$('.scrollTop').length)
            $('#nav-main').toggleClass('scrollTop');
    } else {
        // Scroll vers le bas
        if (lastScrollTop >= $(window).height() - 100 && $('.scrollTop').length)
            $('#nav-main').removeClass('scrollTop');
    }
    lastScrollTop = $(window).scrollTop();
});
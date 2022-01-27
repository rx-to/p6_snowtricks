let scrollTop = 0;
let windowHeight = $(window).height();
let documentHeight = $("body").height();
let scrollButton = $("#scrollArrow");

if (documentHeight < windowHeight * 2) $("body:not(#accueil) #scrollArrow").remove();

scrollButton.on("click", function (e) {
	if ($("html").scrollTop() > $("html").height() * 0.75) scrollTop = 0;
	else scrollTop = windowHeight - $("#nav-main").height();
	$("html, body").animate({ scrollTop: scrollTop });
});

$(document).scroll(function () {
	if ($("html").scrollTop() > $("html").height() * 0.75) scrollButton.css("transform", "rotate(-180deg)");
	else scrollButton.css("transform", "none");
});

$('.btn-see-medias').on('click', function(){
    $($(this).attr('data-target')).fadeIn();
    $(this).fadeOut();
});

$('.btn-hide-medias').on('click', function(){
    $($(this).attr('data-target')).fadeOut();
    $('.btn-see-medias').fadeIn();
});

tinymce.init({
    selector: '.tinymce',
    plugin: 'a_tinymce_plugin',
    a_plugin_option: true,
    a_configuration_option: 400
  });

  $('#category').select2({
    placeholder: 'Sélectionnez une catégorie',
    width: '100%'
  });

AOS.init({ duration: 750 });

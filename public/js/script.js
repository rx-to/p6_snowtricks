let scrollTop = 0;
let windowHeight = $(window).height();
let documentHeight = $("body").height();
let scrollButton = $("#scroll-arrow");

if (documentHeight < windowHeight * 2) $("body:not(#accueil) #scroll-arrow").remove();

scrollButton.on("click", function (e) {
	if ($("html").scrollTop() > $("html").height() * 0.75) scrollTop = 0;
	else scrollTop = windowHeight - $("#nav-main").height();

	$("html, body").animate({ scrollTop: scrollTop });
});

$(document).scroll(function () {
	if ($("html").scrollTop() > $("html").height() * 0.75) {
		if (!scrollButton.hasClass("scroll-up")) scrollButton.toggleClass("scroll-up scroll-down");
	} else if (!scrollButton.hasClass("scroll-down")) {
		scrollButton.toggleClass("scroll-up scroll-down");
	}
});

$(".btn-see-medias").on("click", function () {
	$($(this).attr("data-target")).fadeIn();
	$(this).fadeOut();
});

$(".btn-hide-medias").on("click", function () {
	$($(this).attr("data-target")).fadeOut();
	$(".btn-see-medias").fadeIn();
});

tinymce.init({
	selector: ".tinymce",
	plugin: "a_tinymce_plugin",
	a_plugin_option: true,
	a_configuration_option: 400,
});

$("#category").select2({
	placeholder: "Sélectionnez une catégorie",
	width: "100%",
});

AOS.init({ duration: 750 });

var page = 1;
$("#btn-see-more-tricks").on("click", function () {
	let button = $(this);
	page++;
	$.ajax({
		method: "POST",
		url: "/figures/more/",
		data: { page: page },
	}).done(function (result) {
		$(".tricklist").append(result.tricklist.content);
		if (page == result.countPages) button.remove();
	});
});

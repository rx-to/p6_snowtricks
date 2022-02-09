/* Animations */
AOS.init({ duration: 750 });

/* Textareas */
tinymce.init({
	selector: ".tinymce",
	plugin: "a_tinymce_plugin",
	a_plugin_option: true,
	a_configuration_option: 400,
});

/* Scroll arrow */
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

/* Trick template */
$(".btn-see-medias").on("click", function () {
	$($(this).attr("data-target")).fadeIn();
	$(this).fadeOut();
});

$(".btn-hide-medias").on("click", function () {
	$($(this).attr("data-target")).fadeOut();
	$(".btn-see-medias").fadeIn();
});

$("#category").select2({
	placeholder: "Sélectionnez une catégorie",
	width: "100%",
});

/* Tricklist */
var page = 1;

$("#btn-see-more-tricks").on("click", function () {
	moreItems("figures", $(".tricklist"), $(this));
});

function moreItems(type, container, button) {
	page++;
	$.ajax({
		method: "POST",
		url: "/" + type + "/voir-plus/",
		data: { page: page },
	}).done(function (result) {
		container.append(result.list.content);
		if (page == result.countPages) button.remove();
	});
}

function deleteItem(type, id, container) {
	$.ajax({
		method: "POST",
		url: "/" + type + "/supprimer/",
		data: { id: id },
	}).done(function (result) {
		if (result.success) refreshItems(type, page, container);

		$(".alert")
			.toggleClass("alert-" + (result.success ? "success" : "danger"))
			.html(result.feedback)
			.fadeIn();
	});
}

function refreshItems(type, pageMax, container) {
	$.ajax({
		method: "POST",
		url: "/" + type + "s/rafraichir/",
		data: { pageMax: pageMax },
	}).done(function (result) {
		container.html(result.list.content);
	});
}

var modal = $("#delete-item-modal");

$(document).on("click", "button[data-action=call-popup-delete-item]", function () {
	let itemToDelete = $(this).closest("*[data-item-type]");
	modal.attr("data-item-type", itemToDelete.attr("data-item-type"));
	modal.attr("data-item-id", itemToDelete.attr("data-item-id"));
});

$("button[data-action=delete-item]").on("click", function () {
	let type = modal.attr("data-item-type");
	let id = modal.attr("data-item-id");
	let container = $("[data-item-type=" + type + "]").closest(".list");
	deleteItem(type, id, container);
});

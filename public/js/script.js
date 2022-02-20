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
var curPage = 1;
var lastPage;

function buttonEvent(button, status, container = null) {
	let buttonSelector = $("#" + button.attr("id"));
	let data = {};
	if (status === "on") {
		$(buttonSelector).on("click", function (e) {
			let itemsType = button.attr("data-items-type");
			if (itemsType == "messages") data = { trickID: button.attr("data-trick-id") };
			moreItems(itemsType, container, $(buttonSelector), data);
		});
	} else {
		$(buttonSelector).off("click");
	}
}

function moreItems(type, container, button, extraData = {}) {
	curPage++;
	buttonEvent(button, "off");
	let data = { curPage: curPage };
	if (extraData) {
		data = extraData;
		data.curPage = curPage;
	}
	$.ajax({
		method: "POST",
		url: "/" + type + "/voir-plus/",
		data: data,
	}).done(function (result) {
		pageMax = result.countPages;
		container.append(result.list.content);
		if (curPage == pageMax) button.remove();
		else buttonEvent(button, "on", container);
	});
}

function deleteItem(type, id, container) {
	$.ajax({
		method: "POST",
		url: "/" + type + "/supprimer/",
		data: { id: id },
	}).done(function (result) {
		if (result.success) refreshItems(type, curPage, container, result);
	});
}

function refreshItems(type, pageMax, container, feedback) {
	$.ajax({
		method: "POST",
		url: "/" + type + "s/rafraichir/",
		data: { pageMax: pageMax },
	}).done(function (result) {
		curPage = result.curPage;
		pageMax = result.countPages;
		container.html(result.list.content);
		if (feedback) {
			$(".alert")
				.toggleClass("alert-" + (feedback.success ? "success" : "danger"))
				.html(feedback.feedback)
				.fadeIn();
			setTimeout(function () {
				$(".alert").fadeOut();
			}, 3000);
		}
	});
}

function fillModal(title) {
	modal.find(".modal-body strong").html(title);
}

var modal = $("#delete-item-modal");

$(document).on("click", "button[data-action=call-popup-delete-item]", function () {
	let itemToDelete = $(this).closest("[data-item-type]");
	let itemTitle = $(this).closest("[data-item-type]").find("figcaption a").html();

	modal.attr("data-item-type", itemToDelete.attr("data-item-type"));
	modal.attr("data-item-id", itemToDelete.attr("data-item-id"));

	fillModal(itemTitle);
});

$(document).on("click", "button[data-action=delete-item]", function () {
	let type = modal.attr("data-item-type");
	let id = modal.attr("data-item-id");
	let container = $(".tricks-block");

	deleteItem(type, id, container);
});

var seeMoreTricksButton = $("#btn-see-more-tricks");
var seeMoreMessagesButton = $("#btn-see-more-messages");
if (seeMoreTricksButton.length) buttonEvent(seeMoreTricksButton, "on", $(".tricklist"));
if (seeMoreMessagesButton.length) buttonEvent(seeMoreMessagesButton, "on", $(".comment-list"));

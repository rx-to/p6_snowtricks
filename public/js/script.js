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

function deleteItem(type, id, container, extraData = {}) {
	let data = { id: id };
	if (extraData) {
		data = extraData;
		data.id = id;
	}
	$.ajax({
		method: "POST",
		url: "/" + type + "/supprimer/",
		data: data,
	}).done(function (result) {
		if ($("#template-figure").length) {
			displayAlert(result);
		} else if (result.success) {
			refreshItems(type, curPage, container, result);
		}

		if (result.redirect)
			setTimeout(function () {
				document.location = result.redirect;
			}, 3000);
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
			displayAlert(feedback);
		}
	});
}

function displayAlert(result) {
	$(".alert")
		.toggleClass("alert-" + (result.success ? "success" : "danger"))
		.html(result.feedback)
		.fadeIn();
	setTimeout(function () {
		$(".alert").fadeOut();
	}, 3000);
}

function fillModal(title) {
	modal.find(".modal-body strong").html(title);
}

var modal = $("#delete-item-modal");

$(document).on("click", "button[data-action=call-popup-delete-item]", function () {
	let itemToDelete = $(this).closest("[data-item-type]");
	let itemTitle = itemToDelete.attr("data-item-title");
	let redirect = itemToDelete.attr("data-item-redirect");

	modal.attr("data-item-type", itemToDelete.attr("data-item-type"));
	modal.attr("data-item-id", itemToDelete.attr("data-item-id"));

	if (redirect) modal.attr("data-item-redirect", itemToDelete.attr("data-item-id"));

	fillModal(itemTitle);
});

$(document).on("click", "button[data-action=delete-item]", function () {
	let type = modal.attr("data-item-type");
	let id = modal.attr("data-item-id");
	let container = $(".tricks-block");
	let extraData = {};
	if (modal.attr("data-item-redirect")) extraData = { redirect: 1 };
	deleteItem(type, id, container, extraData);
});

var seeMoreTricksButton = $("#btn-see-more-tricks");
var seeMoreMessagesButton = $("#btn-see-more-messages");
if (seeMoreTricksButton.length) buttonEvent(seeMoreTricksButton, "on", $(".tricklist"));
if (seeMoreMessagesButton.length) buttonEvent(seeMoreMessagesButton, "on", $(".comment-list"));

let imgWrapperIndex = 0;
$(document).on("change", ".upload-image-btn input", function (e) {
	showPreview(e, $(this).closest(".upload-image-btn"));
	if ($(this).closest(".upload-image-btn-wrapper").attr("data-index") == 0) $(".upload-image-btn-wrapper[data-index=0]").find('.thumbnail-btn').click();
	if ($(".upload-image-btn-wrapper:not(.has-img)").length == 0) {
		imgWrapperIndex++;
		$(".files-upload-container").append('<div class="col-sm-3 col-6"><div class="upload-image-btn-wrapper" data-index="' + imgWrapperIndex + '"><i class="fa fa-image thumbnail-btn" title="Définir comme miniature"></i><i class="fa fa-times delete-btn" title="Supprimer l\'image"></i><label class="upload-image-btn"><input type="file" name="newImage[]" class="d-none"></label></div></div>');
	}
});

$(document).on("click", ".thumbnail-btn", function (e) {
	$(".is-thumbnail").removeClass("is-thumbnail");
	if (!$(this).closest(".upload-image-btn-wrapper").hasClass("is-thumbnail")) $(this).closest(".upload-image-btn-wrapper").addClass("is-thumbnail");
	$("input[name=thumbnail]").val($(this).closest(".is-thumbnail").attr("data-index"));
	$(".trick__header").css("background", $(this).closest(".upload-image-btn-wrapper").find(".upload-image-btn").css("background"));
});

$(document).on("click", ".delete-btn", function (e) {
	$(this).closest(".col-sm-3").remove();
});

function showPreview(e, target) {
	if (e.target.files.length > 0) {
		var src = URL.createObjectURL(e.target.files[0]);
		if (!$(target).closest(".upload-image-btn-wrapper").hasClass("has-img")) $(target).closest(".upload-image-btn-wrapper").addClass("has-img");
		target.css("background", "url(" + src + ") no-repeat center / cover");
	}
}

$(".select2-tags").select2({
	tags: true,
	placeholder: "Collez vos embed codes ici !",
});

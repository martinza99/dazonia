$(function () {
	$(".deleteButton").click(function () {
		if (confirm("Delete " + this.parentElement.parentElement.id))
			deleteFile(this);
	});
	$(".deleteAllButton").click(function () {
		if (confirm($(".deleteButton").length + ' pictures will be deleted')) {
			$(".deleteButton").each(deleteFiles);
			setInterval(doneDeleting, 500);
		}
	});

	$(".changeName").click(swapToInput);
	$(".updateName").click(updateName);
	$(".changeNameInput").keydown(isEnter);
	$(".star").click(openStars);
});

function doneDeleting() {
	if ($(".deleteButton").length == 0)
		window.location.href = ".";
}

function deleteFiles(index, _btn) {
	deleteFile(_btn);
}

function deleteFile(_btn) {
	var tr = _btn.parentElement.parentElement;
	$.post("action.php",
		{
			action: "deleteFile",
			fileName: tr.id
		},
		function (response, status, xhr) {
			try {
				response = JSON.parse(response);
				if (response.success == true) {
					tr.remove();
				}
				else
					throw new Error(response.error);
			}
			catch (error) {
				alert(error);
				throw error;
			}
		}
	);
}

function swapToInput() {
	$(this).hide();
	$(this).next().show();
}

function updateName() {
	var tr = $(this).closest("tr")[0];
	var val = $(this).prev().val();
	if (val == "") {
		alert("Empty filename");
		return;
	}
	$.post("action.php",
		{
			action: "updateFile",
			fileName: tr.id,
			newName: val
		},
		function (response, status, xhr) {
			try {
				response = JSON.parse(response);
				if (response.success == true) {
					$(tr).find(".changeName").show();
					$(tr).find(".changeName").text(val);
					$(tr).find(".changeNameInput").hide();
					$(tr).find(".changeNameInput input").css("color", "");
				}
				else
					throw new Error(response.error);
			}
			catch (error) {
				$(tr).find(".changeNameInput input").css("color", "red");
				throw error;
			}
		}
	);
}

function isEnter(_event) {
	if (_event.key == "Enter")
		this.children[1].click();
}

function makeStar(_starElement, _rating) {
	let star = document.createElement("img");
	star.addEventListener("click", sendRating)
	star.src = "img/" + _rating + ".png";
	star.classList = "star " + _rating;
	_starElement.appendChild(star);
	$(star).animate({ left: _rating * 32 + "px" });
}

function openStars() {
	const starWrapper = document.createElement("div");
	starWrapper.className = "tempStar";
	for (let index = 0; index <= 10; index++)
		makeStar(starWrapper, index);
	this.parentElement.appendChild(starWrapper);
}

function sendRating() {
	let val = parseInt(this.classList[1]);
	this.style.zIndex = 100;


	let star = $(this).siblings(".star");
	let tr = $(this).closest("tr")[0];
	let temp = this;
	$(".tempStar").remove();
	$.post("action.php",
		{
			action: "rateFile",
			fileName: tr.id,
			rating: val
		},
		function (response, status, xhr) {
			try {
				response = JSON.parse(response);
				if (response.success == true) {
					tr.querySelector("[title=Average]").src = "img/" + response.avgrating + ".png";
					if (USERNAME != undefined) {
						let target = tr.querySelector("[title=" + USERNAME + "]");
						if (target == null) {
							target = document.createElement("img");
							target.title = USERNAME;
							target.classList = "star userStar";
							tr.querySelector(".starContainer").appendChild(target);
						}
						if (val == 0)
							$(target).remove();
						target.src = "img/" + val + ".png";
					}
				}
				else
					throw new Error(response.error);
			}
			catch (error) {
				alert(error);
				throw error;
			}
		}
	);
}

let tagName = null;

function tagSelect(button) {
	button.style.borderColor = "lime";
	tagName = prompt("Tag name").toLowerCase().trim().replace(/\s+/g, " ").replace(/ /g, "_");
	if (tagName == null) {
		button.style.borderColor = "";
		return;
	}

	document.querySelectorAll(".picsList").forEach(div => {
		div.removeEventListener("click", tagCallback);
		div.addEventListener("click", tagCallback);
	});
}

function tagCallback(event) {
	const div = event.path[1];
	event.preventDefault();
	let action = "addTag";
	if (div.classList.contains("tagged"))
		action = "deleteTag"
	let tr = $(div).closest("tr")[0];
	$(div).removeClass("tagged");
	$(div).removeClass("taggedFailed");
	$(div).addClass("taggedPending");
	$.post("action.php",
		{
			action: action,
			fileName: tr.id,
			tagName: tagName
		},
		function (response, status, xhr) {
			try {
				response = JSON.parse(response);
				$(div).removeClass("taggedPending");
				if (response.success == true) {
					if (response.action == "addTag")
						$(div).addClass("tagged");
				}
				else throw new Error(response.error);
			}
			catch (error) {
				$(div).addClass("taggedFailed");
				throw error;
			}
		},
	);
}
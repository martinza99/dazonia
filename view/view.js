/*jshint esversion: 6 */
let slide;

$(function () {
    $("#fileUp").change(sendReplace);
    $(".star").click(openStars);
    $(".ogButton").click(sendTag);
    $(".tagInput").keydown(isEnter);
    $(".tagInput").keyup(replaceChars);
    $(".deleteTag").click(deleteTag);
});


function swapHandle(data, status, _xhr) {
    swapPic(_xhr, true);
}

function swapPic(_xhr, _push) {
    if (_xhr != "none") {
        $(".pic").attr("src", location.origin + "/files/" + _xhr);
        let stateObj = {
            pic: _xhr
        };
        document.title = _xhr;
        if (_push) {
            history.pushState(stateObj, _xhr, location.origin + "/view/" + _xhr);
            document.querySelector(".hiddenVal").value = location.origin + "/files/" + _xhr;
        }
    }
}

function keyDown(_event) {
    if (document.activeElement.classList.contains("disableHotkeys"))
        return;
    switch (_event.key) {
        case 'c':
            let hid = document.querySelector(".hiddenVal");
            hid.select();
            break;
        case 'ArrowRight':
        case 'd':
            document.querySelector("#next").click();
            break;
        case 'ArrowLeft':
        case 'a':
            document.querySelector("#prev").click();
            break;
        case 'f':
            if (!document.fullscreen)
                document.querySelector("#centerImage").requestFullscreen();
            else
                document.exitFullscreen();
            break;
    }
    if(!isNaN(_event.key)){
        let fileName = location.pathname.match(/\/view\/(.*)/)[1];
        let value = _event.key;
        if(value==0)
            value = 10;
        sendRatingValue(fileName, value);
    }
}

function sendReplace() {
    this.parentElement.submit();
}

function makeStar(_starElement, _rating) {
    let star = document.createElement("img");
    star.addEventListener("click", sendRating)
    star.src = "../list/img/" + _rating + ".png";
    star.classList = "star tempStar " + _rating;
    _starElement.parentElement.appendChild(star);
    $(star).animate({ left: _rating * 32 + "px" });
}

function openStars() {
    for (let index = 0; index <= 10; index++)
        makeStar(this, index);
}

function sendRating() {
    let val = this.classList[2];
    this.style.zIndex = 100;
    $(this).siblings(".tempStar").remove();
    let star = $(".star")[0];
    $(this).remove();
    let fileName = location.pathname.match(/\/view\/(.*)/)[1];
    $.post("../list/action.php",
        {
            action: "rateFile",
            fileName: fileName,
            rating: val
        },
        function (response, status, xhr) {
            try {
                response = JSON.parse(response);
                if (response.success == true)
                    $(star).attr("src", "../list/img/" + response.avgrating + ".png");
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

function sendRatingValue(id, value) {
    const star = $(".star")[0];
    $.post("../list/action.php",
        {
            action: "rateFile",
            fileName: id,
            rating: value
        },
        function (response, status, xhr) {
            try {
                response = JSON.parse(response);
                if (response.success == true)
                    $(star).attr("src", "../list/img/" + response.avgrating + ".png");
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

function sendTag() {
    let tagName = $(".tagInput").val();
    $(".tagInput").val("");
    let fileName = location.pathname.match(/\/view\/(.*)/)[1];
    let temp = this;
    $.post("../list/action.php",
        {
            action: "addTag",
            fileName: fileName,
            tagName: tagName,
        },
        function (response, status, xhr) {
            try {
                response = JSON.parse(response);
                if (response.success == true) {
                    let container = document.createElement("div");
                    container.classList = "sugg";

                    let link = document.createElement("a");
                    link.href = location.origin + "/list?q=tag%3A" + tagName;
                    link.target = "_top";
                    link.innerText = tagName.toLowerCase();
                    container.appendChild(link);

                    let delBut = document.createElement("span");
                    delBut.classList = "deleteTag glyphicon glyphicon-remove";
                    delBut.addEventListener("click", deleteTag);
                    container.appendChild(delBut);

                    let parent = document.querySelector(".tagContainer");//insert as 2nd last element
                    parent.insertBefore(container, parent.childNodes[parent.childElementCount - 2]);
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

function isEnter(_event) {

    if (_event.key == "Enter")
        sendTag();
}

function deleteTag() {
    var tr = $(this).closest(".sugg");
    var tagName = $(this).closest(".sugg").children()[0].text;
    let fileName = location.pathname.match(/\/view\/(.*)/)[1];
    $.post("../list/action.php",
        {
            action: "deleteTag",
            fileName: fileName,
            tagName: tagName,
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

function replaceChars() {//tag input keyup callback
    if (this.value.toLowerCase() != this.value) // if contains upperCase
        this.value = this.value.toLowerCase();
    while (this.value.includes(" "))
        this.value = this.value.replace(" ", "_");
}

function stopSlide() {
    if (slide != undefined)
        clearInterval(slide);
}
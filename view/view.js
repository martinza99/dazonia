/*jshint esversion: 6 */
$(function(){
    //$("#prev").click(updateNext);
    //$("#next").click(updatePrev);
    $("#fileUp").change(sendReplace);
    $(".star").click(openStars);
    $(".ogButton").click(sendTag);
    $(".tagInput").keydown(isEnter);
    $(".deleteTag").click(deleteTag);
});

window.onpopstate = function(_event){
    if(_event.state.pic!=undefined)
        swapPic(eve_eventnt.state.pic);
};

function updateNext(){
    var src = $(".pic").attr("src").slice(9);
    $.get("sibling.php",{s:"n",id:src},swapPic,"text");   
}

function updatePrev(){
    var src = $(".pic").attr("src").slice(9);
    $.get("sibling.php",{s:"p",id:src},swapPic,"text");
}

function swapHandle(data, status, _xhr){
    swapPic(_xhr,true);
}

function swapPic(_xhr,_push){
    if(_xhr!="none"){
        $(".pic").attr("src",location.origin + "/files/" +_xhr);
        let stateObj = {
            pic: _xhr
        };
        document.title = _xhr;
        if(_push){
            history.pushState(stateObj, _xhr, location.origin + "/view/?id=" + xhr);
            document.querySelector(".hiddenVal").value = location.origin + "/files/" +_xhr;
        }
    }
}

function keyDown(_event){
    if(document.querySelector(".tagInput")==document.activeElement)
        return;
    switch(_event.key){
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
    }
}

function sendReplace(){
    this.parentElement.submit();
}

function makeStar(_starElement,_rating){
    let star = document.createElement("img");
    star.addEventListener("click",sendRating)
    star.src = "../list/img/"+_rating+".png";
    star.classList = "star tempStar "+_rating;
    _starElement.parentElement.appendChild(star);
    $(star).animate({left:_rating*32+"px"});
}

function openStars(){
    for (let index = 0; index <= 10; index++)
        makeStar(this,index);
}

function sendRating(){
    let val = this.classList[2];
    this.style.zIndex = 100;
    $(this).siblings(".tempStar").remove();
    let star = $(".star")[0];
    $(this).remove();
    let urlParams = new URLSearchParams(window.location.search);
    let picId = urlParams.get("id");
    $.post("../list/rate.php",
    {
        id: picId,
        rating: val
    },
        function(_response){
            $(star).attr("src","../list/img/"+_response+".png");
        }
    );
}

function sendTag(){
    let tagName = $(".tagInput").val();
    $(".tagInput").val("");
    let urlParams = new URLSearchParams(window.location.search);
    let picId = urlParams.get("id");
    let temp = this;
    $.post("../list/tag.php",
    {
        id: picId,
        tag: tagName,
        action: "c"
    },
    function(_response){
        if(_response=="error")
            return;
        let container = document.createElement("div");
        container.classList = "sugg";
        
        let link = document.createElement("a");
        link.href = location.origin + "/list?q=tag%3A"+tagName;
        link.target = "_top";
        link.innerText = tagName;
        container.appendChild(link);

        let delBut = document.createElement("span");
        delBut.classList = "deleteTag glyphicon glyphicon-remove";
        delBut.addEventListener("click",deleteTag);
        container.appendChild(delBut);

        let parent = document.querySelector(".tagContainer");//insert as 2nd last element
        parent.insertBefore(container,parent.childNodes[parent.childElementCount-2]);
    }
    );
}

function isEnter(_event){
    if(_event.key == "Enter")
        sendTag();
}

function deleteTag() {
    var tr = $(this).closest(".sugg");    
    var tagName = $(this).closest(".sugg").children()[0].text;
    let urlParams = new URLSearchParams(window.location.search);
    let picId = urlParams.get("id");
    $.post("../list/tag.php",
    {
        tag: tagName,
        id: picId,
        action: "d"
    },
        function(){tr.remove();}
    );
}
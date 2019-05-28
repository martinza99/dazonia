/*jshint esversion: 6 */
$(function(){
    //$("#prev").click(updateNext);
    //$("#next").click(updatePrev);
    $("#fileUp").change(sendReplace);
    $(".star").click(openStars);
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
        $(".pic").attr("src","http://dazonia.xyz/files/"+_xhr);
        let stateObj = {
            pic: _xhr
        };
        document.title = _xhr;
        if(_push){
            history.pushState(stateObj, _xhr, "http://dazonia.xyz/view/?id="+xhr);
            document.querySelector(".hiddenVal").value = "http://dazonia.xyz/files/"+_xhr;
        }
    }
}

function keyDown(_event){
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
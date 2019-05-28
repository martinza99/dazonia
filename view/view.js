/*jshint esversion: 6 */
$(function(){
    //$("#prev").click(updateNext);
    //$("#next").click(updatePrev);
    $("#fileUp").change(sendReplace);
    $(".star").click(openStars);
});

window.onpopstate = function(event){
    if(event.state.pic!=undefined)
        swapPic(event.state.pic);
};

function updateNext(){
    var src = $(".pic").attr("src").slice(9);
    $.get("sibling.php",{s:"n",id:src},swapPic,"text");   
}

function updatePrev(){
    var src = $(".pic").attr("src").slice(9);
    $.get("sibling.php",{s:"p",id:src},swapPic,"text");
}

function swapHandle(data, status, xhr){
    swapPic(xhr,true);
}

function swapPic(xhr,push){
    if(xhr!="none"){
        $(".pic").attr("src","http://dazonia.xyz/files/"+xhr);
        let stateObj = {
            pic: xhr
        };
        document.title = xhr;
        if(push){
            history.pushState(stateObj, xhr, "http://dazonia.xyz/view/?id="+xhr);
            document.querySelector(".hiddenVal").value = "http://dazonia.xyz/files/"+xhr;
        }
    }
}

function keyDown(event){
    switch(event.key){
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

function makeStar(starElement,rating,func){
    let star = document.createElement("img");
    star.addEventListener("click",func)
    star.src = "../list/img/"+rating+".png";
    star.classList = "star tempStar "+rating;
    starElement.parentElement.appendChild(star);
    $(star).animate({left:rating*32+"px"});
}

function openStars(){
    makeStar(this,1,redGClick);
    makeStar(this,2,redClick);
    makeStar(this,3,orangeGClick);
    makeStar(this,4,orangeClick);
    makeStar(this,5,greenGClick);
    makeStar(this,6,greenClick);
    makeStar(this,7,blueGClick);
    makeStar(this,8,blueClick);
    makeStar(this,9,purpleGClick);
    makeStar(this,10,purpleClick);
}

function sendRating(starElement,val){
    starElement.style.zIndex = 100;
    $(starElement).siblings(".tempStar").remove();
    let star = $(".star")[0];
    $(starElement).remove();
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

function redGClick(){
    sendRating(this,1);
}

function redClick(){
    sendRating(this,2);
}

function orangeGClick(){
    sendRating(this,3);
}

function orangeClick(){
    sendRating(this,4);
}

function greenGClick(){
    sendRating(this,5);
}

function greenClick(){
    sendRating(this,6);
}

function blueGClick(){
    sendRating(this,7);
}

function blueClick(){
    sendRating(this,8);
}

function purpleGClick(){
    sendRating(this,9);
}

function purpleClick(){
    sendRating(this,10);
}
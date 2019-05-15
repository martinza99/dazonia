/*jshint esversion: 6 */
$(function(){
    //$("#prev").click(updateNext);
    //$("#next").click(updatePrev);
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
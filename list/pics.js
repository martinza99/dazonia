$(function(){
    $(".deleteButton").click(function(){
        if(confirm("Delete "+this.parentElement.parentElement.id))
        deleteFile(this);
    });
    $(".deleteAllButton").click(function(){        
        if(confirm($(".deleteButton").length + ' pictures will be deleted')){
            $(".deleteButton").each(deleteFiles);
            setInterval(doneDeleting, 500);
        }
    });

    $(".changeName").click(swapToInput);
    $(".updateName").click(updateName);
    $(".changeNameInput").keydown(isEnter);
    $(".star").click(openStars);
});

function doneDeleting(){
    if($(".deleteButton").length==0)
        window.location.href = ".";
}

function deleteFiles(i,_btn) {
    deleteFile(_btn);
}

function deleteFile(_btn) {
    var tr = _btn.parentElement.parentElement;
    $.post("delete.php",
    {
        id: tr.id
    },
        function(){tr.remove();}
    );
}

function swapToInput() {
    $(this).hide();
    $(this).next().show();
}

function updateName() {
    var tr = $(this).closest("tr")[0];
    var val = $(this).prev().val();
    if(val == ""){
        alert("Empty filename");
        return;
    }
    $.post("update.php",
    {
        id: tr.id,
        newName: val
    },
        function(_response){
            $(tr).find(".changeName").show();
            $(tr).find(".changeName").text(val);
            $(tr).find(".changeNameInput").hide();
        }
    );
}

function isEnter(event){
    if(event.key == "Enter")
        this.children[1].click();
}

function makeStar(starElement,rating,func){
    let star = document.createElement("img");
    star.addEventListener("click",func)
    star.src = "img/"+rating+".png";
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
    let star = $(starElement).siblings(".star");
    let tr = $(starElement).closest("tr")[0];
    let temp = starElement;
    $(starElement).remove();
    $.post("rate.php",
    {
        id: tr.id,
        rating: val
    },
        function(_response){
            $(star).attr("src","img/"+_response+".png");
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
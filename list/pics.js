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
    var tr = this.closest("tr")[0];
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
        console.log(this.children[1].click());
}

function makeStar(starElement,color,moveLeft,func){
    let star = document.createElement("img");
    star.addEventListener("click",func)
    star.src = "img/"+color+".png";
    star.classList = "star tempStar "+color;
    starElement.parentElement.appendChild(star);
    $(star).animate({left:moveLeft+"px"});
}

function openStars(){
    makeStar(this,"redGray",32,redGClick);
    makeStar(this,"red",64,redClick);
    makeStar(this,"orangeGray",96,orangeGClick);
    makeStar(this,"orange",128,orangeClick);
    makeStar(this,"greenGray",160,greenGClick);
    makeStar(this,"green",192,greenClick);
    makeStar(this,"blueGray",224,blueGClick);
    makeStar(this,"blue",256,blueClick);
    makeStar(this,"purpleGray",288,purpleGClick);
    makeStar(this,"purple",320,purpleClick);
}

function sendRating(starElement,val,color){
    starElement.style.zIndex = 100;
    $(starElement).siblings(".star").attr("src","img/"+color+".png");
    $(starElement).siblings(".tempStar").remove();
    
    let tr = $(starElement).closest("tr")[0];
    let temp = starElement;
    $(starElement).remove();
    $.post("rate.php",
    {
        id: tr.id,
        rating: val
    });
}

function redGClick(){
    sendRating(this,1,"redGray");
}

function redClick(){
    sendRating(this,2,"red");
}

function orangeGClick(){
    sendRating(this,3,"orangeGray");
}

function orangeClick(){
    sendRating(this,4,"orange");
}

function greenGClick(){
    sendRating(this,5,"greenGray");
}

function greenClick(){
    sendRating(this,6,"green");
}

function blueGClick(){
    sendRating(this,7,"blueGray");
}

function blueClick(){
    sendRating(this,8,"blue");
}

function purpleGClick(){
    sendRating(this,9,"purpleGray");
}

function purpleClick(){
    sendRating(this,10,"purple");
}
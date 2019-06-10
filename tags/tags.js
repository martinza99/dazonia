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

    $(".nameScript").contextmenu(updateName);
    $(".thumbScript").contextmenu(changeImage);
    $(".fileUp").change(function(){
        this.parentElement.submit();
    });
});

function changeImage(){
    $(".tagNameInput").val(this.nextSibling.nextSibling.innerText);
    $(".fileUp").click();
}

function doneDeleting(){
    if($(".deleteButton").length==0)
        window.location.href = ".";
}

function deleteFiles(index,_btn) {
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

function updateName() {
    event.preventDefault();
    let element = this;
    let name = prompt("New name",element.innerText);
    if(!name)
        return;
    $.post("index.php",
    {
        tagName: element.innerText,
        newName: name,
        action: "name"
    },
        function(_response){
            if(_response=="Tag updated")
                element.innerText = name;
            else
                alert(_response);
        }
    );
}

function isEnter(_event){
    if(_event.key == "Enter")
        this.children[1].click();
}

function makeStar(_starElement,_rating){
    let star = document.createElement("img");
    star.addEventListener("click",sendRating)
    star.src = "img/"+_rating+".png";
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
    
        
    let star = $(this).siblings(".star");
    let tr = $(this).closest("tr")[0];
    let temp = this;
    $(this).remove();
    $.post("rate.php",
    {
        id: tr.id,
        rating: val
    },
        function(_response){
            $(star).attr("src","img/"+_response+".png");
            if(USERID!=undefined)
                $(star).siblings("."+USERID+"star").attr("src","img/"+val+".png");
        }
    );
}
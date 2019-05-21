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

function openStars(){
    let redG = document.createElement("img");
    redG.addEventListener("click",redGClick)
    redG.src = "img/redGray.png";
    redG.classList = "star tempStar redG";
    this.parentElement.appendChild(redG);
    
    let red = document.createElement("img");
    red.addEventListener("click",redClick)
    red.src = "img/red.png";
    red.classList = "star tempStar red";
    this.parentElement.appendChild(red);
    
    let orangeG = document.createElement("img");
    orangeG.addEventListener("click",orangeGClick)
    orangeG.src = "img/orangeGray.png";
    orangeG.classList = "star tempStar orangeG";
    this.parentElement.appendChild(orangeG);

    let orange = document.createElement("img");
    orange.addEventListener("click",orangeClick)
    orange.src = "img/orange.png";
    orange.classList = "star tempStar orange";
    this.parentElement.appendChild(orange);

    let greenG = document.createElement("img");
    greenG.addEventListener("click",greenGClick)
    greenG.src = "img/greenGray.png";
    greenG.classList = "star tempStar greenG";
    this.parentElement.appendChild(greenG);

    let green = document.createElement("img");
    green.addEventListener("click",greenClick)
    green.src = "img/green.png";
    green.classList = "star tempStar green";
    this.parentElement.appendChild(green);

    let blueG = document.createElement("img");
    blueG.addEventListener("click",blueGClick)
    blueG.src = "img/blueGray.png";
    blueG.classList = "star tempStar blue";
    this.parentElement.appendChild(blueG);

    let blue = document.createElement("img");
    blue.addEventListener("click",blueClick)
    blue.src = "img/blue.png";
    blue.classList = "star tempStar blue";
    this.parentElement.appendChild(blue);

    let purpleG = document.createElement("img");
    purpleG.addEventListener("click",purpleGClick)
    purpleG.src = "img/purpleGray.png";
    purpleG.classList = "star tempStar purple";
    this.parentElement.appendChild(purpleG);

    let purple = document.createElement("img");
    purple.addEventListener("click",purpleClick)
    purple.src = "img/purple.png";
    purple.classList = "star tempStar purple";
    this.parentElement.appendChild(purple);

    $(redG).animate({left:"32px"});
    $(red).animate({left:"64px"});
    $(orangeG).animate({left:"96px"});
    $(orange).animate({left:"128px"});
    $(greenG).animate({left:"160px"});
    $(green).animate({left:"192px"});
    $(blueG).animate({left:"224px"});
    $(blue).animate({left:"256px"});
    $(purpleG).animate({left:"288px"});
    $(purple).animate({left:"320px"});
}

function redGClick(){
    this.style.zIndex = 100;
    $(this).siblings(".star").attr("src","img/redGray.png");
    $(this).siblings(".tempStar").remove();
    
    let tr = $(this).closest("tr")[0];
    let temp = this;
    $(this).remove();
    $.post("rate.php",
    {
        id: tr.id,
        rating: 1
    });
}

function redClick(){
    this.style.zIndex = 100;
    $(this).siblings(".star").attr("src","img/red.png");
    $(this).siblings(".tempStar").remove();
    
    let tr = $(this).closest("tr")[0];
    let temp = this;
    $(this).remove();
    $.post("rate.php",
    {
        id: tr.id,
        rating: 2
    });
}

function orangeGClick(){
    this.style.zIndex = 100;
    $(this).siblings(".star").attr("src","img/orangeGray.png");
    $(this).siblings(".tempStar").remove();
    
    let tr = $(this).closest("tr")[0];
    let temp = this;
    $(this).remove();
    $.post("rate.php",
    {
        id: tr.id,
        rating: 3
    });
}

function orangeClick(){
    this.style.zIndex = 100;
    $(this).siblings(".star").attr("src","img/orange.png");
    $(this).siblings(".tempStar").remove();
    
    let tr = $(this).closest("tr")[0];
    let temp = this;
    $(this).remove();
    $.post("rate.php",
    {
        id: tr.id,
        rating: 4
    });
}

function greenGClick(){
    this.style.zIndex = 100;
    $(this).siblings(".star").attr("src","img/greenGray.png");
    $(this).siblings(".tempStar").remove();
    
    let tr = $(this).closest("tr")[0];
    let temp = this;
    $(this).remove();
    $.post("rate.php",
    {
        id: tr.id,
        rating: 5
    });
}

function greenClick(){
    this.style.zIndex = 100;
    $(this).siblings(".star").attr("src","img/green.png");
    $(this).siblings(".tempStar").remove();
    
    let tr = $(this).closest("tr")[0];
    let temp = this;
    $(this).remove();
    $.post("rate.php",
    {
        id: tr.id,
        rating: 6
    });
}

function blueGClick(){
    this.style.zIndex = 100;
    $(this).siblings(".star").attr("src","img/blueGray.png");
    $(this).siblings(".tempStar").remove();
    
    let tr = $(this).closest("tr")[0];
    let temp = this;
    $(this).remove();
    $.post("rate.php",
    {
        id: tr.id,
        rating: 7
    });
}

function blueClick(){
    this.style.zIndex = 100;
    $(this).siblings(".star").attr("src","img/blue.png");
    $(this).siblings(".tempStar").remove();
    
    let tr = $(this).closest("tr")[0];
    let temp = this;
    $(this).remove();
    $.post("rate.php",
    {
        id: tr.id,
        rating: 8
    });
}

function purpleGClick(){
    this.style.zIndex = 100;
    $(this).siblings(".star").attr("src","img/purpleGray.png");
    $(this).siblings(".tempStar").remove();
    
    let tr = $(this).closest("tr")[0];
    let temp = this;
    $(this).remove();
    $.post("rate.php",
    {
        id: tr.id,
        rating: 9
    });
}

function purpleClick(){
    this.style.zIndex = 100;
    $(this).siblings(".star").attr("src","img/purple.png");
    $(this).siblings(".tempStar").remove();
    
    let tr = $(this).closest("tr")[0];
    let temp = this;
    $(this).remove();
    $.post("rate.php",
    {
        id: tr.id,
        rating: 10
    });
}
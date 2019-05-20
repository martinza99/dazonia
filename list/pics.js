$(function(){
    $(".deleteButton").click(function(){
        if(confirm("Delete "+$(this).closest("tr")[0].id))
        deleteFile(this);
    });
    $(".deleteAllButton").click(function(){        
        if(confirm($(".deleteButton").length + ' pictures will be deleted')){
            $(".deleteButton").each(deleteFiles);
            setInterval(doneDeleting, 500);
        }
    });

    $(".starButton").click(rate);
    $(".changeName").click(swapToInput);
    $(".updateName").click(updateName);
    $(".changeNameInput").keydown(isEnter);
});

function doneDeleting(){
    if($(".deleteButton").length==0)
        window.location.href = ".";
}

function deleteFiles(i,_btn) {
    deleteFile(_btn);
}

function deleteFile(_btn) {
    var tr = _btn.closest("tr");
    $.post("delete.php",
    {
        id: tr.id
    },
        function(){tr.remove();}
    );
}

function rate() {
    let btn = this;
    let tr = this.closest("tr");
    $(this).parent().prev().text(btn.innerText);
    $.post("rate.php",
    {
        id: tr.id,
        rating: btn.innerText
    })
}

function swapToInput() {
    $(this).hide();
    $(this).next().show();
}

function updateName() {
    var tr = this.closest("tr");
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
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
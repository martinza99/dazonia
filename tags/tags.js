$(function(){
    $(".deleteButton").click(function(){
        if(confirm("Delete "+this.parentElement.parentElement.id))
        deleteTag(this);
    });

    $(".nameScript").contextmenu(updateName);
    $(".parentScript").contextmenu(updateParent);
    $(".thumbScript").contextmenu(changeImage);
    $(".fileUp").change(function(){
        this.parentElement.submit();
    });
});

function changeImage(){
    event.preventDefault();
    $(".tagNameInput").val(this.parentElement.parentElement.id);
    $(".fileUp").click();
}

function deleteTag(_btn) {
    var tr = _btn.parentElement.parentElement;
    $.post("editor.php",
    {
        tagName: tr.id,
        action: "delete"
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
    $.post("editor.php",
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

function updateParent() {
    event.preventDefault();
    let element = this;
    let stagName = this.parentElement.id;
    let parent = prompt("New parent name", element.innerText);
    if(!parent)
        return;
    $.post("editor.php",
    {
        tagName: stagName,
        newParent: parent,
        action: "parent"
    },
        function(_response){
            if(_response=="Parent updated"){
                element.innerText = parent;
            }
            else
                alert(_response);
        }
    );
}
$(function(){
    $(".deleteButton").click(function(){
        if(confirm("Delete "+this.parentElement.parentElement.id))
        deleteTag(this);
    });
    $(".newTagButton").click(newTag);
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

function newTag(){
    let tag = prompt("Tag name");
    if(tag == false)
        return;
    $.post("editor.php",
    {
        tagName: tag,
        action: "new"
    },
    function(_response){
        alert(_response);
        location.reload();
    });
}

function tagSearchFormSubmit() {
    event.preventDefault();
    let query = document.querySelector(".searchInputTags").value;
    let searchLink = document.querySelector(".searchLinkTags");
    searchLink.href += query;
    searchLink.click();
}

function showSearchBar() {
    let btn = document.querySelector(".tagSearchButton");
    if(btn.style.borderTopLeftRadius=="4px"){
        btn.style.borderTopLeftRadius="0px"
        btn.style.borderBottomLeftRadius="0px"
    }
    else{
        btn.style.borderTopLeftRadius="4px"
        btn.style.borderBottomLeftRadius="4px"
    }
    $(".searchInputTags").animate({width:'toggle'},350);
}
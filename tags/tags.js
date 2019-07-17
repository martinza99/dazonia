$(function(){
    $(".deleteButton").click(function(){
        if(confirm("Delete "+this.parentElement.parentElement.id))
        deleteTag(this);
    });
    $(".newTagButton").click(newTagPrompt);
    $(".nameScript").contextmenu(updateName);
    $(".parentScript").contextmenu(updateParentPrompt);
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

function updateParentPrompt() {
    event.preventDefault();
    let element = this;
    let stagName = this.parentElement.id;
    let parent = prompt("New parent name", element.innerText);
    if(parent==null)
        return;
    updateParent(parent, stagName, element);
}

function updateParent(_parent, _stagName, _element) {
    $.post("editor.php",
    {
        tagName: _stagName,
        newParent: _parent,
        action: "parent"
    },
        function(_response){
            if(_response=="Parent updated"){
                _element.innerText = _parent;
            }
            else if(_response=="Parent doesn't exist"){
                if(confirm("Parent doesn't exist, create \"" + _parent +"\"?")){
                    createNewTag(_parent, false);
                    updateParent(_parent, _stagName, _element);
                }
            }
            else
                alert(_response);
        }
    );
}

function newTagPrompt(){
    let tag = prompt("Tag name");
    if(tag == null)
        return;
    createNewTag(tag, true);
}

function createNewTag(_tag, _showResponse) {
    $.post("editor.php",
    {
        tagName: _tag,
        action: "new"
    },
    function(_response){
        if(_showResponse)
            alert(_response);
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
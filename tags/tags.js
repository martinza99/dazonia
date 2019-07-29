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
    let td = this; //table data cell
    let tr = td.parentElement; //table row
    let a = td.children[0]; //link
    let newName = prompt("New name",a.innerText);
    if(!newName)
        return;
    $.post("editor.php",
    {
        tagName: a.innerText,
        newName: newName,
        action: "name"
    },
        function(_response){
            if(_response=="Tag updated"){
                a.href = a.href.substr(0, a.href.lastIndexOf(td.innerText)) + newName; //replace link href
                a.innerText = newName; //replace link text
                tr.id = newName; //replace table row id
            }
            else
                alert(_response);
        }
    );
}

function updateParentPrompt() {
    event.preventDefault();
    let td = this;
    let currentTagName = this.parentElement.id;
    let parent = prompt("New parent name", td.innerText);
    if(parent == null)
        return;
    updateParent(parent, currentTagName, td);
}

function updateParent(_newParent, _currentTagName, _td) {
    $.post("editor.php",
    {
        tagName: _currentTagName,
        newParent: _newParent,
        action: "parent"
    },
        function(_response){
            switch (_response) {
                case "Parent updated":
                    switch (_newParent) {
                        case "root":
                            _newParent = "(root)";
                            break;
                        case " ":
                            _newParent = "(no parent)";
                            break;
                        default:
                            break;
                    }
                    _td.innerHTML = "";//clear td content
                    let prevDomain = _td.previousSibling.children[0].href; //get href from prev element 
                    let domain = prevDomain.substr(0,prevDomain.lastIndexOf("/list/")); // extract domain
                    let a = document.createElement("a"); //create a tag
                    a.href = domain + "/tags?t=" + _newParent; //set href
                    a.target = "_top"; //set target
                    a.innerText = _newParent; //set link text
                    _td.appendChild(a); //append link
                    break;
                case "Parent doesn't exist":
                    if(confirm("Parent doesn't exist, create \"" + _newParent +"\"?")){
                        createNewTag(_newParent, false);
                        updateParent(_newParent, _currentTagName, _td);
                    }
                    break;
                default:
                    alert(_response);
        }
    });
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
$(function () {
    $(".deleteButtonToken").click(function () {

        if (confirm("Delete " + $(this).closest("tr")[0].id))
            deleteToken(this);
    });
    $(".deleteAllButtonToken").click(function () {
        if (confirm($(".deleteButtonToken").length + ' Tokens will be deleted')) {
            $(".deleteButtonToken").each(deleteToken);
        }
    });
    $(".deleteButtonUser").click(function () {

        if (confirm("Delete " + $(this).closest("tr")[0].id))
            deleteUser(this);
    });
});

function equals(_source, _target) {
    let sourceValue = _source.value;
    let targetValue = document.querySelector(_target).value;
    let submitButton = document.querySelector("#submitButton");
    if (sourceValue != "" && sourceValue == targetValue) {//if strings not empty and match up
        submitButton.disabled = false;//enable submit button
        return true;
    }
    else {
        submitButton.disabled = true;//disable submit button
        return false;
    }
}

function copyKey(target) {
    let node = document.querySelector("." + target);
    let promise = navigator.clipboard.writeText(node.innerText);
    promise.then(function () {
        console.log("copied to Clipboard!");
    }, function (params) {
        alert(params);
        console.trace(params);
    })

}

function setForm(_value) {
    document.querySelector(".formAction").value = _value;
    document.querySelector(".queryForm").submit();
}

function createToken() {
    $.post("token.php", { action: "c" }, location.reload());
}

function deleteTokens(i, _btn) {
    deleteToken(_btn);
}

function deleteToken(_btn) {
    var tr = _btn.closest("tr");
    $.post("token.php",
        {
            id: tr.id,
            action: "d"
        },
        function () { tr.remove(); }
    );
}

function deleteUser(_btn) {
    var tr = _btn.closest("tr");
    $.post("users.php",
        {
            id: tr.id,
            action: "d"
        },
        function () { tr.remove(); }
    );
}
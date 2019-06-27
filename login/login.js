function equals(_source, _target) {
    let sourceValue = _source.value;
    let targetValue = document.querySelector(_target).value;
    let submitButton = document.querySelector("#submitButton");
    if(sourceValue != "" && sourceValue == targetValue){//if strings not empty and match up
        submitButton.disabled = false;//enable submit button
        return true;
    }
    else{
        submitButton.disabled = true;//disable submit button
        return false;
    }
}
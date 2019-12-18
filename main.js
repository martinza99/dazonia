$(function () {
    $(".navbarMargin").css("height", $(".navbar").css("height"));
})

function searchFormSubmit() {
    event.preventDefault();
    let query = document.querySelector(".searchInput").value;
    let searchLink = document.querySelector(".searchLink");
    searchLink.href += encodeURIComponent(query);
    searchLink.click();
}
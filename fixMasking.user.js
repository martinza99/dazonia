// ==UserScript==
// @name         dazoniaFixMaskin
// @namespace    https://github.com/martinza99/dazonia
// @version      0.1
// @description  fixes domain masking stuff
// @author       martin
// @match        http://dazonia.xyz/*
// @grant        none
// ==/UserScript==

(function() {
    'use strict';
    let link = document.createElement("link");
    link.rel = "shortcut icon";
    link.href = "https://ma.2ix.ch/favicon";
    link.type = "image/x-icon";
    $("head").appendChild(link);
});
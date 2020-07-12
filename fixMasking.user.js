// ==UserScript==
// @name         dazoniaFixMasking
// @namespace    https://github.com/martinza99/dazonia
// @version      0.3
// @description  fixes domain masking stuff
// @author       martin
// @match        https://dazonia.xyz/*
// @include      https://*.dazonia.xyz/*
// @grant        none
// ==/UserScript==

var favicon_link_html = document.createElement("link");
favicon_link_html.rel = "icon";
favicon_link_html.href = "https://ma.2ix.ch/favicon";
favicon_link_html.type = "image/x-icon";

try {
	document.getElementsByTagName("head")[0].appendChild(favicon_link_html);
} catch (e) {}

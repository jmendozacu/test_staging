/**
 * Created by florian on 21.03.16.
 */


var dropCookie = true;
var cookieDuration = 3600;
var cookieName = 'cookie-richtlinie';
var cookieValue = 'on';

function createDiv(){
    var div = '<div id="cookie-law"><p>Perfekt-schlafen.de verwendet Cookies, um Ihnen den bestmöglichen Service zu gewährleisten.<br>Wenn Sie auf der Seite weitersurfen, stimmen Sie der <a href="/cookie-nutzung/" rel="nofollow" title="Cookie-Nutzung">Cookie-Nutzung</a> zu. <a class="close-cookie-banner" href="javascript:void(0);" onclick="removeMe();"><span onclick="createCookie(window.cookieName,window.cookieValue, window.cookieDuration);">» Ich bin einverstanden</span></a></p></div>';
    jQuery('body').append(div);
}


function createCookie(name,value,days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        var expires = "; expires="+date.toGMTString();
    }
    else var expires = "";
    if(window.dropCookie) {
        document.cookie = name+"="+value+expires+"; path=/";
    }
}

function checkCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}

function eraseCookie(name) {
    createCookie(name,"",-1);
}

window.onload = function(){
    if(checkCookie(window.cookieName) != window.cookieValue){
        createDiv();
    }
}

function removeMe(){
    var element = document.getElementById('cookie-law');
    element.parentNode.removeChild(element);
}

// hide an element
function hideElement(strID, blnUseDisplay)
{
    var xmlNode = $(strID);
    if(xmlNode) {
        blnUseDisplay ? xmlNode.style.display = "none" : xmlNode.style.visibility = "hidden";
    }
}

// makes an element visible
function showElement(strID, blnUseDisplay)
{
    var xmlNode = $(strID);
    if(xmlNode) {
        blnUseDisplay ? xmlNode.style.display = "block" : xmlNode.style.visibility = "visible";
    }
}

// remove leading and trailing spaces
function trim(s)
{
    while (s.substring(0,1) == ' ') {
        s = s.substring(1,s.length);
    }
    while (s.substring(s.length-1,s.length) == ' ') {
        s = s.substring(0,s.length-1);
    }
    return s;
}

function replace(string, searchStr, replaceStr)
{
    var new_string='';
    var i=0;
    while(i<string.length) {
        if(string.substring(i,searchStr.length)==searchStr) {
            new_string+=replaceStr;
            i+=searchStr.length;
        } else {
            new_string+=string.charAt(i);
            i++;
        }
    }
    return new_string;
}

// get displayed text inside a node (and child nodes)
function getNodeText(xmlNode, blnSkipReplace)
{
    var strNodeText = '';
    var xmlChildNodes = xmlNode.childNodes;
    for(var i = 0; i < xmlChildNodes.length; i++){
        if(xmlChildNodes[i].nodeType == 3) {  // text node
            strNodeText += xmlChildNodes[i].data;
        } else {
            strNodeText += getNodeText(xmlChildNodes[i], true);
        }
    }
    if(blnSkipReplace)
        return strNodeText;
    else
        return trim(strNodeText.replace(/\s+/g, ' '));
}


function getWindowWidth()
{
    if(window.innerWidth)
        return innerWidth;
    // Internet Explorer
    return document.body.offsetWidth;
}


function getWindowHeight()
{
    if(window.innerHeight)
        return innerHeight;
    // Internet Explorer
    return document.body.offsetHeight;
}

function getTopScroll()
{
    // Internet Explorer
    if(window.event) {
        return document.documentElement.scrollTop ;
    } else {
        return pageYOffset ;
    }
}

function setSelect(objID, selValue)
{
    var selObject = $(objID);
    for (var i=0; i<selObject.options.length; i++) {
        if (selObject.options[i].value == selValue) {
            selObject.options[i].selected = true;
        } else {
            selObject.options[i].selected = false;
        }
    }
}

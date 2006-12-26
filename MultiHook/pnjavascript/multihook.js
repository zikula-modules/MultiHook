/**
 *
 * $Id$
 *
 */

function addEventHandlers()
{
    var elementid;
    document.getElementsByClassName('multihookeditlink').each(
        function(editlink, index) {
            elementid = 'editlink_' + index + '_' + editlink.title.split('#')[1];
            editlink.id = elementid;
            Event.observe(
                          elementid,
                          'click',
                          function(clickevent) {
                             starteditmultihook(clickevent);
                          },
                          false );
        }
        );
}

function starteditmultihook(clickevent)
{
    // set the parent objects id for finding it later
    Event.element(clickevent).parentNode.id = 'mh_update_content';

    showInfo(loadingText, objMouseXY.xpos, objMouseXY.ypos, false);

    var eventparams = Event.element(clickevent).id.split('_');
    var pars = "module=MultiHook&func=read&mh_aid=" + eventparams[2];
    var myAjax = new Ajax.Request(
        "ajax.php",
        {
            method: 'post',
            parameters: pars,
            onComplete: editmultihook
        });

}

function editmultihook(originalRequest)
{
    hideInfo();
    // show error if necessary
    if( originalRequest.status != 200 ) {
        showajaxerror(originalRequest);
    } else {
        var abac = mhdejsonize(originalRequest.responseText);

        $("mhedit_aid").value      = abac['aid'];
        $("mhedit_short").value    = abac['short'];
        $("mhedit_long").value     = abac['long'];
        $("mhedit_title").value    = abac['title'];
        setSelect('mhedit_type', abac['type']);
        setSelect('mhedit_language', abac['language']);
        $("mhedit_delete").checked = false;

        var objMultiHook = $('multihookedit');
        objMultiHook.style.left = objMouseXY.xpos + 'px';
        objMultiHook.style.top  = objMouseXY.ypos + 'px';
        objMultiHook.style.visibility = "visible";
    }
}

function submiteditmultihook()
{
    $('multihookedit').style.visibility = 'hidden';
    showInfo(savingText, objMouseXY.xpos, objMouseXY.ypos, false);

    var pars = "module=MultiHook&func=store" +
               "&mh_aid=" + $F('mhedit_aid') +
               "&mh_short=" + encodeURIComponent($F('mhedit_short')) +
               "&mh_long=" + encodeURIComponent($F('mhedit_long')) +
               "&mh_title=" + encodeURIComponent($F('mhedit_title')) +
               "&mh_type=" + $F('mhedit_type') +
               "&mh_delete=" + $F('mhedit_delete') +
               "&mh_language=" + $F('mhedit_language');
    var myAjax = new Ajax.Request(
                    "ajax.php",
                    {
                        method: 'post',
                        parameters: pars,
                        onComplete: submiteditmultihook_response
                    }
                    );

}

function submiteditmultihook_response(originalRequest)
{
    hideInfo();
    // show error if necessary
    if( originalRequest.status != 200 ) {
        showajaxerror(originalRequest);
    } else {
        var json = mhdejsonize(originalRequest.responseText);
        $('mh_update_content').innerHTML = json.data;
        $('mh_update_content').id = '';
    
    }
    addEventHandlers();

}

function submitmultihook()
{
    $('multihook').style.visibility = 'hidden';
    showInfo(savingText, objMouseXY.xpos, objMouseXY.ypos, false);

    if((objMHSelection.parentObj != 'undefined') && (objMHSelection.parentObj != null)) {
        var newtext = "<span id='mh_new_content'>" + $('mh_short').value + "</span>";
        var oldregexp = eval( '/' + $('mh_short').value + '/g');
        objMHSelection.parentObj.innerHTML = objMHSelection.parentObj.innerHTML.replace(oldregexp, newtext);
    }

    var pars = "module=MultiHook&func=store" +
               "&mh_short=" + encodeURIComponent($F('mh_short')) +
               "&mh_long=" + encodeURIComponent($F('mh_long')) +
               "&mh_title=" + encodeURIComponent($F('mh_title')) +
               "&mh_type=" + $F('mh_type') +
               "&mh_language=" + $F('mh_language');
    var myAjax = new Ajax.Request(
                    "ajax.php",
                    {
                        method: 'post',
                        parameters: pars,
                        onComplete: submitmultihook_response}
                    );
}

// shows the new entry as tool tip
// process data from server
function submitmultihook_response(originalRequest)
{
    hideInfo();
    // show error if necessary
    if( originalRequest.status != 200 ) {
        showajaxerror(originalRequest);
    } else {
        var json = mhdejsonize(originalRequest.responseText);
        $('mh_new_content').innerHTML = json.data;
        $('mh_new_content').id = '';
    
    }
    addEventHandlers();

    objMHSelection.text           = '';
    //objMHSelection.selection      = null;
    objMHSelection.isSelected     = false;
    objMHSelection.parentObj      = 'undefined';

}

function cancelmultihook()
{
    var updateObj = $('mh_update_content');
    if(updateObj) {
        updateObj.id = '';
    }
    var newObj = $('mh_new_content');
    if(newObj) {
        newObj.id = '';
    }
    $('multihook').style.visibility='hidden';
    $('multihookedit').style.visibility='hidden';

    objMHSelection.text           = '';
    //objMHSelection.selection      = null;
    objMHSelection.isSelected     = false;
    objMHSelection.parentObj      = 'undefined';
}

function showInfo(text, xpos, ypos, showclose)
{
    var infoObj = $('multihookinformation');
    if(showclose == true) {
        $('multihookinformationclose').style.visibility = 'visible';
        $('multihookindicator').style.visibility = 'hidden';
        
    } else {
        $('multihookinformationclose').style.visibility = 'hidden';
        $('multihookindicator').style.visibility = 'visible';
    }
    $('multihookinformationcontent').innerHTML = text;
    infoObj.style.left = xpos + 'px';
    infoObj.style.top  = ypos + 'px';
    infoObj.style.visibility = "visible";
}

function hideInfo()
{
    $('multihookinformationclose').style.visibility = 'hidden';
    $('multihookindicator').style.visibility = 'hidden';
    $('multihookinformation').style.visibility = "hidden";
}

function showajaxerror(ajaxRequest)
{
    // no success
    showInfo(ajaxRequest.responseText, objMouseXY.xpos, objMouseXY.ypos, true);
}

// get mouse coords on mouseup event to get selection end
function stopSelection(objEvent)
{
    if($('multihook').style.visibility=='visible' ||
       $('multihookedit').style.visibility=='visible' ||
       $('multihookinformation').style.visibility=='visible') {
        return;
    }
    objMouseXY.getXY(objEvent);

    if(objMouseXY.ctrlkey == true) {
        objMHSelection.update();
        if(objMHSelection.isSelected) {

            var objMultiHook = $( "multihook" );
            $('mh_short').value = objMHSelection.text.strip(); // strip = trim
            $('mh_long').value = '';
            $('mh_title').value = '';
            setSelect('mh_type', 0);
            setSelect('mh_language', 'all');

            objMultiHook.style.left = objMouseXY.xpos + 'px';
            objMultiHook.style.top  = objMouseXY.ypos + 'px';

            objMultiHook.style.visibility = "visible";
        }
    }
}

function MouseXY( )
{
    this.xpos      = 0;
    this.ypos      = 0;
    this.getXY = getMouseXY;
    this.ctrlkey = false;
}

function getMouseXY(objEvent)
{
    if($('multihook').style.visibility=='visible' ||
       $('multihookedit').style.visibility=='visible' ||
       $('multihookinformation').style.visibility=='visible') {
        return;
    }

    // Internet Explorer
    if( window.event ) {
        this.xpos = event.clientX;
        this.ypos = event.clientY + document.documentElement.scrollTop ;
        this.ctrlkey = event.ctrlKey;
    }
    // w3c
    else {
        this.xpos = objEvent.pageX;
        this.ypos = objEvent.pageY;
        this.ctrlkey = objEvent.ctrlKey;
    }

}

// class to hold information about current selection
function MHSelectedText( )
{
    this.text           = '';
    //this.selection      = null;
    this.isSelected     = false;
    this.parentObj      = 'undefined';
    this.update         = getSelectedText;
}

// retrieve information about the selected text
function getSelectedText()
{
    // mozilla
    if(this.isSelected) {
        return;
    }
    if($('multihook').style.visibility=='visible') {
        return;
    }

    var selection;
    if( window.getSelection )
    {
        selection = window.getSelection();
        if(selection) {
            this.text = selection + '';
            if(selection.anchorNode) {
                this.parentObj = selection.anchorNode.parentNode;
            }
            this.isSelected = this.text.length;
        }
    }
    // opera
    else if( document.getSelection )
    {
        selection = document.getSelection();
        if(selection) {
            this.text = selection;
            this.parentObj = selection.parent;
            //alert(this.parentObj);
            this.isSelected = this.text.length;
        }
    }
    // internet explorer
    else {
        selection = document.selection.createRange();
        if(selection) {
            this.text = selection.text;
            this.parentObj = selection.parentElement();
            this.isSelected = this.text.length;
        }
    }
}

/**
 * mhdejsonize
 * unserializes an array
 *
 *@param jsondata JSONized array in utf-8 (as created by AjaxUtil::output
 *@return array
 */
function mhdejsonize(jsondata)
{
    var result;
    try {
        result = eval('(' + jsondata + ')');
    } catch(error) {
        alert('illegal JSON response: \n' + error + 'in\n' + jsondata);
    }
    return result;
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

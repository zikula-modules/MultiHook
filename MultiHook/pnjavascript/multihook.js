/**
 *
 * $Id$
 *
 */

function mhinit()
{
    objMHSelection = new MHSelection();
    
    // check mouse position at selection end
    Event.observe(document, 'mouseup', stopSelection, false);

    new Draggable('multihook', {handle: 'multihookheader'});
    new Draggable('multihookedit', {handle: 'multihookheader'});
    
    addMHEventHandlers();
}

function addMHEventHandlers()
{
    var elementid;
    document.getElementsByClassName('multihookeditlink').each(
        function(editlink, index) {
            elementid = 'editlink_' + index + '_' + editlink.title.split('#')[1];
            editlink.id = elementid;
            Event.observe(elementid, 'click', starteditmultihook, false);
        }
        );
}

function starteditmultihook(clickevent)
{
    // set the parent objects id for finding it later
    Event.element(clickevent).parentNode.id = 'mh_update_content';

    showInfo(loadingText, objMHSelection.xpos, objMHSelection.ypos, false);

    var pars = 'module=MultiHook&func=read&mh_aid=' + Event.element(clickevent).id.split('_')[2];
    var myAjax = new Ajax.Request(
        'ajax.php',
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

        $('mhedit_aid').value      = abac['aid'];
        $('mhedit_short').value    = abac['short'];
        $('mhedit_long').value     = abac['long'];
        $('mhedit_title').value    = abac['title'];
        setSelect('mhedit_type', abac['type']);
        setSelect('mhedit_language', abac['language']);
        $('mhedit_delete').checked = false;

        var objMultiHook = $('multihookedit');
        objMultiHook.style.left = objMHSelection.xpos + 'px';
        objMultiHook.style.top  = objMHSelection.ypos + 'px';
        objMultiHook.show();
    }
}

function submiteditmultihook()
{
    $('multihookedit').hide();
    showInfo(savingText, objMHSelection.xpos, objMHSelection.ypos, false);

    var pars = 'module=MultiHook&func=store' +
               '&mh_aid=' + $F('mhedit_aid') +
               '&mh_short=' + encodeURIComponent($F('mhedit_short')) +
               '&mh_long=' + encodeURIComponent($F('mhedit_long')) +
               '&mh_title=' + encodeURIComponent($F('mhedit_title')) +
               '&mh_type=' + $F('mhedit_type') +
               '&mh_delete=' + $F('mhedit_delete') +
               '&mh_language=' + $F('mhedit_language');
    var myAjax = new Ajax.Request(
                    'ajax.php',
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
        $('mh_update_content').update(mhdejsonize(originalRequest.responseText).data);
        $('mh_update_content').id = '';
    }
    addMHEventHandlers();
}

function submitmultihook()
{
    $('multihook').hide();
    showInfo(savingText, objMHSelection.xpos, objMHSelection.ypos, false);

    if((objMHSelection.parentObj != 'undefined') && (objMHSelection.parentObj != null)) {
        var newtext = "<span id='mh_new_content'>" + $('mh_short').value + "</span>";
        var oldregexp = eval( '/' + $('mh_short').value + '/g');
        objMHSelection.parentObj.update(objMHSelection.parentObj.innerHTML.replace(oldregexp, newtext));
    }

    var pars = 'module=MultiHook&func=store' +
               '&mh_short=' + encodeURIComponent($F('mh_short')) +
               '&mh_long=' + encodeURIComponent($F('mh_long')) +
               '&mh_title=' + encodeURIComponent($F('mh_title')) +
               '&mh_type=' + $F('mh_type') +
               '&mh_language=' + $F('mh_language');
    var myAjax = new Ajax.Request(
                    'ajax.php',
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
        $('mh_new_content').update(mhdejsonize(originalRequest.responseText).data);
        $('mh_new_content').id = '';
    }
    addMHEventHandlers();

    objMHSelection.text           = '';
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
    $('multihook').hide();
    $('multihookedit').hide();

    objMHSelection.text           = '';
    objMHSelection.parentObj      = 'undefined';
}

function showInfo(text, xpos, ypos, showclose)
{
    var infoObj = $('multihookinformation');
    if(showclose == true) {
        $('multihookinformationclose').show();
        $('multihookindicator').hide();
        
    } else {
        $('multihookinformationclose').hide();
        $('multihookindicator').show();
    }
    $('multihookinformationcontent').update(text);
    infoObj.style.left = xpos + 'px';
    infoObj.style.top  = ypos + 'px';
    infoObj.show();
}

function hideInfo()
{
    $('multihookinformationclose').hide();
    $('multihookindicator').hide();
    $('multihookinformation').hide();
}

function showajaxerror(ajaxRequest)
{
    // no success
    showInfo(ajaxRequest.responseText, objMHSelection.xpos, objMHSelection.ypos, true);
}

// get mouse coords on mouseup event to get selection end
function stopSelection(objEvent)
{
    if($('multihook').visible()  ||
       $('multihookedit').visible() ||
       $('multihookinformation').visible()) {
        return;
    }
    objMHSelection.getXY(objEvent);

    if(objMHSelection.ctrlkey == true) {
        objMHSelection.update();
        if(objMHSelection.text.length != 0) {

            var objMultiHook = $('multihook');
            $('mh_short').value = objMHSelection.text;
            $('mh_long').value = '';
            $('mh_title').value = '';
            setSelect('mh_type', 0);
            setSelect('mh_language', 'all');

            objMultiHook.style.left = objMHSelection.xpos + 'px';
            objMultiHook.style.top  = objMHSelection.ypos + 'px';
            objMultiHook.show();
        }
    }
}

function getMouseXY(objEvent)
{
    if($('multihook').visible() ||
       $('multihookedit').visible() ||
       $('multihookinformation').visible()) {
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
function MHSelection()
{
    this.text           = '';
    this.parentObj      = 'undefined';
    this.update         = getSelectedText;
    //mouse position related
    this.xpos      = 0;
    this.ypos      = 0;
    this.getXY = getMouseXY;
    this.ctrlkey = false;

}

// retrieve information about the selected text
function getSelectedText()
{
    // mozilla
    if(this.text.length != 0) {
        return;
    }
    if($('multihook').visible()) {
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
        }
    }
    // opera
    else if( document.getSelection )
    {
        selection = document.getSelection();
        if(selection) {
            this.text = selection;
            this.parentObj = selection.parent;
        }
    }
    // internet explorer
    else {
        selection = document.selection.createRange();
        if(selection) {
            this.text = selection.text;
            this.parentObj = selection.parentElement();
        }
    }
    this.text.strip();
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
        alert('illegal JSON response: \n' + error + 'in\n' + jsondata.truncate(200, '...'));
    }
    return result;
}

function setSelect(objID, selValue)
{
    var selObject = $(objID);
    for (var i=0; i<selObject.options.length; i++) {
        selObject.options[i].selected = (selObject.options[i].value == selValue)
    }
}

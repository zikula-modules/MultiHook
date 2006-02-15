/**
 *
 * $Id$
 *
 */

function addEventHandlers()
{
    var counter = 0;
    var titleparts, elementid;
    for(var i=0; i < document.links.length; i++) {
        if(document.links[i].text == '+') {
            titleparts = document.links[i].title.split('#');
            elementid = 'editlink_' + counter + '_' + titleparts[1];
            document.links[i].id = elementid;
            Event.observe(
                          elementid, 
                          'click', 
                          function(clickevent) {
                             starteditmultihook(clickevent);
                          },
                          false );
            counter++;
        }
    }
}

function starteditmultihook(clickevent)
{
    // set the parent objects id for finding it later
    Event.element(clickevent).parentNode.id = 'mh_update_content';

    // save th objects id that ause the event    
    eventObjID = Event.element(clickevent).id;

    showInfo(loadingText, objMouseXY.up_xpos, objMouseXY.up_ypos, false);
    
    objMouseXY.backup();
        
    var eventparams = Event.element(clickevent).id.split('_');
    var pars = "module=MultiHook&type=ajax&func=read&mh_aid=" + eventparams[2];
    var myAjax = new Ajax.Request(
        "index.php", 
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
        var abac = originalRequest.responseText.split('$');
        
        $("mhedit_aid").value      = abac[0];
        $("mhedit_short").value    = abac[1];
        $("mhedit_long").value     = abac[2];
        $("mhedit_title").value    = abac[3]; 
        setSelect('mhedit_type', abac[4]);
        setSelect('mhedit_language', abac[5]);
        $("mhedit_delete").checked = false;
        
        var objMultiHook = $('multihookedit');
        objMultiHook.style.left = objMouseXY.up_xpos + 'px';
        objMultiHook.style.top  = objMouseXY.up_ypos + 'px';
        objMultiHook.style.visibility = "visible";
    }
}

function submiteditmultihook()
{
    hideElement('multihookedit');
    showInfo(savingText, objMouseXY.lastup_xpos, objMouseXY.lastup_ypos, false);

    var pars = "module=MultiHook&type=ajax&func=store" + 
               "&mh_aid=" + $F('mhedit_aid') + 
               "&mh_short=" + $F('mhedit_short') + 
               "&mh_long=" + $F('mhedit_long') + 
               "&mh_title=" + $F('mhedit_title') + 
               "&mh_type=" + $F('mhedit_type') + 
               "&mh_delete=" + $F('mhedit_delete') + 
               "&mh_language=" + $F('mhedit_language') + 
               "&mh_eventobjid=" + eventObjID;

    var myAjax = new Ajax.Updater(
                    {success: 'mh_update_content'},
                    "index.php", 
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
    $('mh_update_content').id = '';

    // add new eventhandler to editlink
    Event.observe(
                  eventObjID, 
                  'click', 
                  function(clickevent) {
                     starteditmultihook(clickevent);
                  },
                  false );

    // show error if necessary
    if( originalRequest.status != 200 ) { 
        showajaxerror(originalRequest);
    }
}

function submitmultihook()
{
    hideElement('multihook');
    showInfo(savingText, objMouseXY.lastup_xpos, objMouseXY.lastup_ypos, false);

    if((objMHSelection.parentObj != 'undefined') && (objMHSelection.parentObj != null)) {
        var newtext = "<span id='mh_new_content'>" + $('mh_short').value + "</span>";
        var oldregexp = eval( '/' + $('mh_short').value + '/g');
        objMHSelection.parentObj.innerHTML = objMHSelection.parentObj.innerHTML.replace(oldregexp, newtext);
    }
    
    var pars = "module=MultiHook&type=ajax&func=store" +
               "&mh_short=" + $F('mh_short') + 
               "&mh_long=" + $F('mh_long') + 
               "&mh_title=" + $F('mh_title') + 
               "&mh_type=" + $F('mh_type') + 
               "&mh_language=" + $F('mh_language');
    var myAjax = new Ajax.Updater(
                    {success: 'mh_new_content'},
                    "index.php", 
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
    $('mh_new_content').id = '';
    
    // show error if necessary
    if( originalRequest.status != 200 ) { 
        showajaxerror(originalRequest);
    }
    
    objMHSelection.text           = '';
    objMHSelection.selection      = null;
    objMHSelection.isSelected     = false;
    objMHSelection.isNew          = false;
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
    objMHSelection.selection      = null;
    objMHSelection.isSelected     = false;
    objMHSelection.isNew          = false;
    objMHSelection.parentObj      = 'undefined';
}

function showInfo(text, xpos, ypos, showclose)
{
    var infoObj = $('multihookinformation');
    if(showclose == true) {
        $('multihookinformationclose').style.visibility = 'visible';
    } else {
        $('multihookinformationclose').style.visibility = 'hidden';
    }
    $('multihookinformationcontent').innerHTML = text;
    infoObj.style.left = xpos + 'px';
    infoObj.style.top  = ypos + 'px';
    infoObj.style.visibility = "visible";
}

function hideInfo()
{
    $('multihookinformationclose').style.visibility = 'hidden';
    $('multihookinformation').style.visibility = "hidden";
}

function showajaxerror(ajaxRequest)
{
    // no success
    showInfo(ajaxRequest.responseText, objMouseXY.lastup_xpos, objMouseXY.lastup_ypos, true);
}

// update mouse coords on mousedown event to get selection start
function startSelection(objEvent)
{
    objMouseXY.getXY(objEvent, true);
}

// get mouse coords on mouseup event to get selection end
function stopSelection(objEvent)
{
    if($('multihook').style.visibility=='visible') {
        return;
    }
    if($('multihookedit').style.visibility=='visible') {
        return;
    }
    if($('multihookinformation').style.visibility=='visible') {
        return;
    }
    objMouseXY.getXY(objEvent);

    if( objMouseXY.isNew ) {
        objMHSelection.update();
        if(objMHSelection.isSelected) {

            var objMultiHook = $( "multihook" );
            $('mh_short').value = trim(objMHSelection.text);
            $('mh_long').value = '';
            $('mh_title').value = '';
            setSelect('mh_type', 0);
            setSelect('mh_language', 'all');
            
            objMouseXY.backup();
            
            objMultiHook.style.left = objMouseXY.up_xpos + 'px';
            objMultiHook.style.top  = objMouseXY.up_ypos + 'px';
            
            objMultiHook.style.visibility = "visible";
        }
    }
}

function MouseXY( )
{
    this.down_xpos    = 0;
    this.down_ypos    = 0;
    this.up_xpos      = 0;
    this.up_ypos      = 0;
    this.lastup_xpos     = 0;
    this.lastup_ypos     = 0;
    this.lastdown_xpos   = 0;
    this.lastdown_ypos   = 0;
    this.isNew    = false;
    this.getXY = getMouseXY;
    this.backup = backupXY;
}
function getMouseXY(objEvent, start)
{
    if($('multihook').style.visibility=='visible') {
        return;
    }
    if($('multihookedit').style.visibility=='visible') {
        return;
    }
    if($('multihookinformation').style.visibility=='visible') {
        return;
    }
    
    // Internet Explorer
    if( window.event ) {
        var intX = event.clientX;
        var intY = event.clientY + getTopScroll( ) ;
    }
    // w3c
    else {
        var intX = objEvent.pageX;
        var intY = objEvent.pageY;
    }
    
    if(start==true) {
        this.down_xpos = intX;
        this.down_ypos = intY;
        this.isNew = start;
    } else {
        this.up_xpos = intX;
        this.up_ypos = intY;
    }
}

function backupXY(objEvent)
{
    this.lastup_xpos     = this.up_xpos;
    this.lastup_ypos     = this.up_ypos;
    this.lastdown_xpos   = this.down_xpos;
    this.lastdown_ypos   = this.down_ypos;
}

// class to hold information about current selection
function MHSelectedText( )
{
    this.text           = '';
    this.selection      = null;
    this.isSelected     = false;
    this.isNew          = false;
    this.parentObj      = 'undefined';
    this.update         = method_getMHSelectedText;
}

// retrieve information about the selected text
function method_getMHSelectedText()
{
    // mozilla
    if(this.isSelected) {
        return;
    }
    if($('multihook').style.visibility=='visible') {
        return;
    }
    
    if( window.getSelection )
    {
        this.selection = window.getSelection( );
        this.text = this.selection + ''; // ).replace( /\n/g, ":::" );
        this.parentObj = this.selection.anchorNode.parentNode;
        //alert(this.parentObj);
        this.isSelected = this.text.length;
    }
    // opera
    else if( document.getSelection )
    {
        this.selection = document.getSelection();
        this.text = this.selection; //.replace( /\n/g, ":::" );
        this.parentObj = this.selection.parent;
        //alert(this.parentObj);
        this.isSelected = this.text.length;
    }
    // internet explorer
    else {
        this.selection = document.selection.createRange();
        this.text = this.selection.text; //.replace( /\n/g, ":::" );
        this.parentObj = this.selection.parentElement();
        this.isSelected = this.text.length;
    }
}
    
    
    
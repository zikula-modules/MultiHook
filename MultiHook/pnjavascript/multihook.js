    
function starteditmultihook(aid, parent)
{
    parent.id = 'mh_update_content';

    showInfo(loadingText, objMouseXY.xpos, objMouseXY.ypos, false);
    objMouseXY.lastxpos = objMouseXY.xpos;
    objMouseXY.lastypos = objMouseXY.ypos;
        
    var pars = "module=MultiHook&type=ajax&func=read&mh_aid=" + aid;
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
        objMultiHook.style.left = objMouseXY.xpos + 'px';
        objMultiHook.style.top  = objMouseXY.ypos + 'px';
        objMultiHook.style.visibility = "visible";
    }
}

function submiteditmultihook()
{
    hideElement('multihookedit');
    showInfo(savingText, objMouseXY.lastxpos, objMouseXY.lastypos, false);

    var pars = "module=MultiHook&type=ajax&func=store" + 
               "&mh_aid=" + $F('mhedit_aid') + 
               "&mh_short=" + $F('mhedit_short') + 
               "&mh_long=" + $F('mhedit_long') + 
               "&mh_title=" + $F('mhedit_title') + 
               "&mh_type=" + $F('mhedit_type') + 
               "&mh_delete=" + $F('mhedit_delete') + 
               "&mh_language=" + $F('mhedit_language');
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

    // show error if necessary
    if( originalRequest.status != 200 ) { 
        showajaxerror(originalRequest);
    }
}

function submitmultihook()
{
    hideElement('multihook');
    showInfo(savingText, objMouseXY.lastxpos, objMouseXY.lastypos, false);

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
    showInfo(ajaxRequest.responseText, objMouseXY.lastxpos, objMouseXY.lastypos, true);
    
    
    /*
    var objToolTip = $( "multihookinformation" );
    $('multihookinformationclose').style.visibility = 'visible';
    objToolTip.style.width = "auto";
    $('multihookinformationcontent').innerHTML = '<span style="color: #333333;">' + ajaxRequest.responseText + '</span>';
    if( objToolTip.offsetWidth > getWindowWidth( ) / 2 )
        objToolTip.style.width = getWindowWidth( ) / 2 + "px";
    if( objMHSelection.top < objToolTip.offsetHeight )
        objToolTip.style.top = objMHSelection.bottom + 1 + 'px';
    else
        objToolTip.style.top = objMHSelection.top - objToolTip.offsetHeight - 2 + 'px';
    objToolTip.style.left = objMHSelection.left + 'px';
    showElement( "multihookinformation" );
    */
}

// update mouse coords on mousedown event to get selection start
function checkMHSelection1(objEvent)
{
    objMHMouse.update(objEvent);
    objMouseXY.getXY(objEvent);
}

// get mouse coords on mouseup event to get selection end
function checkMHSelection(objEvent)
{
    objMHMouse.update(objEvent);
    if( objMHMouse.isNew )
    {
        objMHSelection.update( objMHMouse.xStart, objMHMouse.xEnd, objMHMouse.yStart, objMHMouse.yEnd );
        if( objMHSelection.isSelected )
        {
            var objMultiHook = $( "multihook" );
            $('mh_short').value = trim(objMHSelection.text);
            $('mh_long').value = '';
            $('mh_title').value = '';
            setSelect('mh_type', 0);
            setSelect('mh_language', 'all');
            objMouseXY.lastxpos = objMouseXY.xpos;
            objMouseXY.lastypos = objMouseXY.ypos;

            objMultiHook.style.left = Math.min( getWindowWidth( ) - objMultiHook.offsetWidth, Math.max( 0, objMHSelection.right - objMultiHook.offsetWidth ) ) + "px";
            if( objMHSelection.top < objMultiHook.offsetHeight )
                objMultiHook.style.top = objMHSelection.bottom + 1 + "px";
            else
                objMultiHook.style.top = objMHSelection.top - objMultiHook.offsetHeight - 1 + "px";
            objMultiHook.style.visibility = "visible";
        }
    }
}

function MouseXY( )
{
    this.xpos     = 0;
    this.ypos     = 0;
    this.lastxpos     = 0;
    this.lastypos     = 0;
    this.getXY = getMouseXY;
}
function getMouseXY( objEvent )
{
    // Internet Explorer
    if( window.event )
    {
        var intX = event.clientX;
        var intY = event.clientY + getTopScroll( ) ;
    }
    // w3c
    else
    {
        var intX = objEvent.pageX;
        var intY = objEvent.pageY;
    }
    
    this.xpos = intX;
    this.ypos = intY;
}

function calcXY(objEvent)
{
    objMouseXY.getXY( objEvent );
}

// class to hold mouse coords
function MHMouse( )
{
    this.intType    = 1;
    this.xStart     = 0;
    this.xEnd       = 0;
    this.yStart     = 0;
    this.yEnd       = 0;
    this.blnInit    = false; 
    this.isNew      = false;
    this.update     = method_setMHMousePosition;
}


// retrieve mouse coords
function method_setMHMousePosition( objEvent )
{
    // Internet Explorer
    if( window.event )
    {
        var intX = event.clientX;
        var intY = event.clientY + getTopScroll( ) ;
    }
    // w3c
    else
    {
        var intX = objEvent.pageX;
        var intY = objEvent.pageY;
    }
    
    if( this.intType % 2 )
    {
        this.xStart = intX;
        this.yStart = intY;
    }
    else
    {
        this.xEnd = intX;
        this.yEnd = intY;
    }
    
    if( this.blnInit )
        this.isNew = this.xStart != this.xEnd || this.yStart != this.yEnd;
    else
        this.blnInit = true;
    this.intType++;
}


// class to hold information about current selection
function MHSelectedText( )
{
    this.top            = 0;
    this.bottom         = 0;
    this.left           = 0;
    this.right          = 0;
    this.text           = '';
    this.selection      = null;
    this.isSelected     = false;
    this.isNew          = false;
    this.parentObj      = 'undefined';
    this.update         = method_getMHSelectedText;
}

// retrieve information about the selected text
function method_getMHSelectedText( intXStart, intXEnd, intYStart, intYEnd )
{
    var intTop = this.top;
    var intBottom = this.bottom;
    var strText = this.text;
    
    // mozilla
    if( window.getSelection )
    {
        this.selection = window.getSelection( );
        this.text = this.selection + ''; // ).replace( /\n/g, ":::" );
        this.parentObj = this.selection.anchorNode.parentNode;
        this.isSelected = this.text.length;

        if( this.isSelected )
        {
            var objNode1 = this.selection.anchorNode.parentNode;
            var objNode2 = this.selection.focusNode.parentNode;
            
            var blnSwap = ( this.top == objNode2.offsetTop && objNode2.offsetTop != objNode1.offsetTop );
            
            var objParent = blnSwap ? objNode2 : objNode1;
            var intStartOffset = Math.min( this.selection.focusOffset, this.selection.anchorOffset );
            var intEndOffset = Math.max( this.selection.focusOffset, this.selection.anchorOffset );

            if( objParent.nodeName.toUpperCase( ) == "BODY" || objParent.nodeName.toUpperCase( ) == "HTML"
                || intStartOffset || this.selection.toString( ).length < getNodeText( objParent ).length )
            {
                if( !isNaN( intXStart ) )
                {
                    this.top = Math.min( intYStart, intYEnd ) - 6;
                    this.right = Math.max( intXStart, intXEnd );
                    this.left = Math.min( intXStart, intXEnd );
                    this.bottom = Math.max( intYStart, intYEnd ) + 6;
                }
            }
            else
            {
                this.top = Math.min( objNode1.offsetTop, objNode2.offsetTop );
                this.left = Math.min( objNode1.offsetLeft, objNode2.offsetLeft );                   
                var objLeft = blnSwap ? objNode2 : objNode1;
                var objClone = objLeft.cloneNode( true );
                objClone.style.visibility = "hidden";
                objClone.style.cssFloat = "left";
                objLeft.parentNode.insertBefore( objClone, objLeft );
                var strContent = objClone.firstChild.nodeValue.substr( 0, intEndOffset );
                if( strContent.substr( strContent.length - 1, 1 ) == ' ' )
                    strContent = strContent.substr( 0, strContent.length - 1 ) + "|" ;
                objClone.firstChild.nodeValue = strContent; 
                this.right = this.left + objClone.offsetWidth;
                if( intStartOffset )
                {   
                    strContent = strContent.substr( 0, intStartOffset );
                    if( strContent.substr( strContent.length - 1, 1 ) == ' ' )
                        strContent = strContent.substr( 0, strContent.length - 1 ) + "|" ;
                    objClone.firstChild.nodeValue = strContent;                 
                    this.left += objClone.offsetWidth;
                }
                objClone.parentNode.removeChild( objClone );
            }
            
            this.bottom = Math.max( objNode1.offsetTop + objNode1.offsetHeight, objNode2.offsetTop + objNode2.offsetHeight );
        }
    }
    // opera
    else if( document.getSelection )
    {
        /*
        var oldtext = document.getSelection(); 
        var newtext = "<span id='mh_new_content'>" + oldtext + "</span>";
        var oldregexp = eval( '/' + oldtext + '/g');
        document.getSelection.anchorNode.parentNode.innerHTML = document.getSelection.anchorNode.parentNode.innerHTML.replace(oldregexp, newtext);
        */
        this.selection = document.getSelection();
        this.text = this.selection.replace( /\n/g, ":::" );
        this.parentObj = this.selection.parentElement;
        //alert(this.parentObj);
        this.isSelected = this.text.length;
        if( !isNaN( intXStart ) )
        {
            this.top = Math.min( intYStart, intYEnd ) - 12;
            this.bottom = Math.max( intYStart, intYEnd ) + 12;
            this.right = Math.max( intXStart, intXEnd );
            this.left = Math.min( intXStart, intXEnd );
        }
    }
    // internet explorer
    else
    {
        this.selection = document.selection.createRange();
        this.text = this.selection.text; //.replace( /\n/g, ":::" );
        this.parentObj = this.selection.parentElement();
        this.isSelected = this.text.length;
        if( this.isSelected )
        {
            this.top = this.selection.offsetTop + getTopScroll( );
            this.left = this.selection.offsetLeft;
            this.bottom = this.top + this.selection.boundingHeight + getTopScroll( );
            this.right = this.left + this.selection.boundingWidth;
        }
    }
    this.isNew = intTop != this.top || intBottom != this.bottom || strText != this.text;
}
    
    
    
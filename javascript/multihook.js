/**
 *
 * $Id: multihook.js 215 2009-11-13 15:56:04Z herr.vorragend $
 *
 */

Event.observe(window, 'load', function() {
    Zikula.define('MultiHook');
});

document.observe('dom:loaded', function() { new MultiHook(); });

var MultiHook = Class.create(
{
    initialize: function()
    {
        
        this.MHselectexdText           = '';
        this.MHparentObj      = 'undefined';
        this.xpos      = 0;
        this.ypos      = 0;

        // check mouse position at selection end
        document.observe('mouseup', this.stopSelection.bind(this));
        new Draggable('multihook', {handle: 'multihookheader'});
        new Draggable('multihookedit', {handle: 'multihookheader'});
        this.addMHEventHandlers();
    },

    addMHEventHandlers: function()
    {
        $$('img.multihookeditlink').each(
            function(editlink, index) {
                elementid = 'editlink_' + index + '_' + editlink.title.split('#')[1];
                editlink.id = elementid;
                Event.observe(elementid, 'click', this.starteditmultihook, false);
            }
            );
    },

    starteditmultihook: function(clickevent)
    {
        // set the parent objects id for finding it later
        Event.element(clickevent).parentNode.id = 'mh_update_content';
    
        this.showInfo(mhloadingText, this.xpos, this.ypos, false);
    
        //var pars = 'module=MultiHook&func=read&mh_aid=' + Event.element(clickevent).id.split('_')[2];
        new Zikula.Ajax.Request(
            'ajax.php?module=MultiHook&func=read&mh_aid=' + Event.element(clickevent).id.split('_')[2],
            {
                method: 'post',
                onComplete: function(req)
                            {
                                this.hideInfo();
                                // show error if necessary
                                if (!req.isSuccess()) {
                                    Zikula.showajaxerror(req.getMessage());
                                    return;
                                }
    
                                abac = req.getData();
                                //var abac = mhdejsonize(originalRequest.responseText);
                        
                                $('mhedit_aid').value      = abac['aid'];
                                $('mhedit_short').value    = abac['short'];
                                $('mhedit_long').value     = abac['long'];
                                $('mhedit_title').value    = abac['title'];
                                this.setSelect('mhedit_type', abac['type']);
                                this.setSelect('mhedit_language', abac['language']);
                                $('mhedit_delete').checked = false;
                        
                                objMultiHook = $('multihookedit');
                                objMultiHook.style.left = this.xpos + 'px';
                                objMultiHook.style.top  = this.ypos + 'px';
                                objMultiHook.show();
                            }
            });
    
    },

    submiteditmultihook: function()
    {
        $('multihookedit').hide();
        this.showInfo(mhsavingText, this.xpos, this.ypos, false);
    
        var pars = 'mh_aid=' + $F('mhedit_aid') +
                   '&mh_short=' + encodeURIComponent($F('mhedit_short')) +
                   '&mh_long=' + encodeURIComponent($F('mhedit_long')) +
                   '&mh_title=' + encodeURIComponent($F('mhedit_title')) +
                   '&mh_type=' + $F('mhedit_type') +
                   '&mh_delete=' + $F('mhedit_delete') +
                   '&mh_language=' + $F('mhedit_language');
        new Zikula.Ajax.Request(
                        'ajax.php?module=MultiHook&func=store',
                        {
                            method: 'post',
                            parameters: pars,
                            onComplete: function(req)
                            {
                                hideInfo();
                                // show error if necessary
                                if (!req.isSuccess()) {
                                    Zikula.showajaxerror(req.getMessage());
                                    return;
                                }
    
                                data = req.getData();
                                $('mh_update_content').update(data);
                                $('mh_update_content').id = '';
                                this.addMHEventHandlers();
                            }
                        }
                        );
    
    },

    submitmultihook: function()
    {
        $('multihook').hide();
        this.showInfo(mhsavingText, this.xpos, this.ypos, false);
    
        if((this.MHparentObj != 'undefined') && (this.MHparentObj != null)) {
            var newtext = "<span id='mh_new_content'>" + $('mhnew_short').value + "</span>";
            var oldregexp = eval( '/' + $('mhnew_short').value + '/g');
            this.MHparentObj.update(this.MHparentObj.innerHTML.replace(oldregexp, newtext));
        }
    
        var pars = 'mh_short=' + encodeURIComponent($F('mhnew_short')) +
                   '&mh_long=' + encodeURIComponent($F('mhnew_long')) +
                   '&mh_title=' + encodeURIComponent($F('mhnew_title')) +
                   '&mh_type=' + $F('mhnew_type') +
                   '&mh_language=' + $F('mhnew_language');
        new Zikula.Ajax.Request(
                        'ajax.php?module=MultiHook&func=store',
                        {
                            method: 'post',
                            parameters: pars,
                            onComplete: function(req)
                                        {
                                            this.hideInfo();
                                            // show error if necessary
                                            if (!req.isSuccess()) {
                                                Zikula.showajaxerror(req.getMessage());
                                                return;
                                            }
                                            
                                            data = req.getData();
                                            $('mh_new_content').update(data);
                                            $('mh_new_content').id = '';
                                            this.addMHEventHandlers();
                                        
                                            this.MHselectexdText  = '';
                                            this.MHparentObj      = 'undefined';
                                        }
                        });
        this.cancelmultihook();
    },

    cancelmultihook: function()
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
    
        this.MHselectexdText  = '';
        this.MHparentObj      = 'undefined';
    },

    showInfo: function(text, xpos, ypos, showclose)
    {
        var infoObj = $('multihookinformation');
        if(this.showclose == true) {
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
    },
    
    hideInfo: function()
    {
        $('multihookinformationclose').hide();
        $('multihookindicator').hide();
        $('multihookinformation').hide();
    },

    // get mouse coords on mouseup event to get selection end
    stopSelection: function(objEvent)
    {
console.log(objEvent);
        if($('multihook').visible()  ||
           $('multihookedit').visible() ||
           $('multihookinformation').visible()) {
            return;
        }

        this.xpos = objEvent.clientX;
        this.ypos = objEvent.clientY;
    
        if(objEvent.ctrlKey == true) {

            // mozilla
            if(this.MHselectedText.length != 0) {
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
                    this.MHselectedText = selection + '';
                    if(selection.anchorNode) {
                        this.MHparentObj = selection.anchorNode.parentNode;
                    }
                }
            }
            // opera
            else if( document.getSelection )
            {
                selection = document.getSelection();
                if(selection) {
                    this.MHselectedText = selection;
                    this.MHparentObj = selection.parent;
                }
            }
            // internet explorer
            else {
                selection = document.selection.createRange();
                if(selection) {
                    this.MHselectedText = selection.text;
                    this.MHparentObj = selection.parentElement();
                }
            }
            this.MHselectexdText.strip();

            if(this.MHselectexdText.length != 0) {
    
                var objMultiHook = $('multihook');
                $('mhnew_short').value = this.text;
                $('mhnew_long').value = '';
                $('mhnew_title').value = '';
                this.setSelect('mhnew_type', 0);
                this.setSelect('mhnew_language', 'all');
    
                objMultiHook.style.left = this.xpos + 'px';
                objMultiHook.style.top  = this.ypos + 'px';
                objMultiHook.show();
            }
        }
    },

    setSelect: function(objID, selValue)
    {
        var selObject = $(objID);
        for (var i=0; i<selObject.options.length; i++) {
            selObject.options[i].selected = (selObject.options[i].value == selValue)
        }
    }
})

<?php
// $Id$
// ----------------------------------------------------------------------
// LICENSE
//
// This program is free software; you can redistribute it and/or
// modify it under the terms of the GNU General Public License (GPL)
// as published by the Free Software Foundation; either version 2
// of the License, or (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// ----------------------------------------------------------------------
// Original Author of file: Frank Schummertz
// Purpose of file:  Initialisation functions for MultiHook
// ----------------------------------------------------------------------

/**
 * initialise the MultiHook module
 */
function MultiHook_init()
{
    // create the MultiHook table
    if (!DBUtil::createTable('multihook')) {
        return false;
    }

    // Set up module variables
    pnModSetVar('MultiHook', 'itemsperpage', 20);
    pnModSetVar('MultiHook', 'abacfirst', 0);
    pnModSetVar('MultiHook', 'mhincodetags', 0);
    pnModSetVar('MultiHook', 'mhlinktitle', 0);
    pnModSetVar('MultiHook', 'mhreplaceabbr', 0);
    pnModSetVar('MultiHook', 'mhshoweditlink', 1);
    
    // collect the needles  
    // force loading of adminapi
    pnModAPILoad('MultiHook', 'admin', true);
    pnModAPIFunc('MultiHook', 'admin', 'collectneedles');
    
    // Set up module hooks
    if (!pnModRegisterHook('item',
                           'transform',
                           'API',
                           'MultiHook',
                           'user',
                           'transform')) {
        pnSessionSetVar('errormsg', _MH_COULDNOTREGISTER);
        return false;
    }

    // silently import autolinks
    if(pnModAvailable('Autolinks')) {
        $als = pnModAPIFunc('Autolinks', 'user', 'getall');
        if(is_array($als)) {
            if(pnModAPILoad('MultiHook', 'admin', true) && pnModAPILoad('MultiHook', 'user', true) ) {
                $mhs = pnModAPIFunc('MultiHook', 'user', 'getall', array('filter'=>2));
                // get the short's only
                $short = array();
                if(is_array($mhs)) {
                    foreach($mhs as $mh) {
                        $short[$mh['short']] = 1;
                    }
                }
                $imported = 0;
                $dupes = 0;
                foreach( $als as $al) {
                    if(!array_key_exists($al['keyword'], $short)) {
                        if( pnModAPIFunc('MultiHook',
                                         'admin',
                                         'create',
                                         array('short' => $al['keyword'],
                                               'long' => $al['url'],
                                               'title' => $al['title'],
                                               'type' => 2,
                                               'language' => "")) >> false ) {
                            $imported++;
                        }
                    } else {
                        $dupes++;
                    }
                }
            }
        }
    }

    // Initialisation successful
    return true;
}

/**
 * upgrade the module from an old version
 */
function MultiHook_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    switch($oldversion) {
        case '1.0':
            pnModSetVar('MultiHook', 'mhincodetags', 0);
        case '1.1':
            pnModSetVar('MultiHook', 'mhlinktitle', 0);
            pnModSetVar('MultiHook', 'mhreplaceabbr', 0);
        case '1.3':
            pnModSetVar('MultiHook', 'mhshoweditlink', 1);
        case '2.0':
        case '3.0':
            // collect the needles  
            // force loading of adminapi
            pnModAPILoad('MultiHook', 'admin', true);
            pnModAPIFunc('MultiHook', 'admin', 'collectneedles');
        case '4.0':
        case '4.5':
            break;
    }
    pnModAPIFunc('pnRender', 'user', 'clear_compiled');

    return true;
}

/**
 * delete the MultiHook module
 */
function MultiHook_delete()
{
    // drop the table
    if (!DBUtil::dropTable('multihook')) {
        return false;
    }

    // Remove module variables
    pnModDelVar('MultiHook');

    // Remove module hooks
    if (!pnModUnregisterHook('item',
                             'transform',
                             'API',
                             'MultiHook',
                             'user',
                             'transform')) {
        return LogUtil::registerError(_MH_COULDNOTUNREGISTER);
        return false;
    }

    // Deletion successful
    return true;
}
?>

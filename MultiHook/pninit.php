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
    // the table definition itself is done in pntables.php
    if (!DBUtil::createTable('multihook')) {
        return LogUtil::registerError(_MH_DBCREATETABLEERROR);
    }

    // Set up module variables
    pnModSetVar('MultiHook', 'itemsperpage', 20);
    pnModSetVar('MultiHook', 'abacfirst', 0);
    pnModSetVar('MultiHook', 'mhincodetags', 0);
    pnModSetVar('MultiHook', 'mhlinktitle', 0);
    pnModSetVar('MultiHook', 'mhreplaceabbr', 0);
    pnModSetVar('MultiHook', 'mhshoweditlink', 1);
    
    // collect the needles  
    // Force loading of adminapi with 3rd parameter set to true. This loads the api although
    // the module is not really available yet. You as the module author are responsible
    // for any side effects now, eg. when calling functions that access database tables or use
    // module vars that have not been set yet.
    pnModAPILoad('MultiHook', 'admin', true);
    pnModAPIFunc('MultiHook', 'admin', 'collectneedles');
    
    // Set up module hooks
    if (!pnModRegisterHook('item',
                           'transform',
                           'API',
                           'MultiHook',
                           'user',
                           'transform')) {
        return LogUtil::registerError(_MH_COULDNOTREGISTER);
    }

    // import autolinks if available
    if(pnModAvailable('Autolinks')) {
        $als = pnModAPIFunc('Autolinks', 'user', 'getall');
        pnModAPILoad('MultiHook', 'user', true);
        $mhs = pnModAPIFunc('MultiHook', 'user', 'getall', array('filter' => 2));
        // get the short's only
        $short = array();
        if(is_array($mhs)) {
            foreach($mhs as $mh) {
                $short[$mh['short']] = 1;
            }
        }
        if(is_array($als)) {
            pnModAPILoad('MultiHook', 'user', true);
            $mhs = pnModAPIFunc('MultiHook', 'user', 'getall', array('filter' => 2));
            // get the short's only
            $short = array();
            if(is_array($mhs)) {
                foreach($mhs as $mh) {
                    $short[$mh['short']] = 1;
                }
            }
            $imported = 0;
            foreach($als as $al) {
                if(!array_key_exists($al['keyword'], $short)) {
                    if( pnModAPIFunc('MultiHook',
                                     'admin',
                                     'create',
                                     array('short' => $al['keyword'],
                                           'long' => $al['url'],
                                           'title' => $al['title'],
                                           'type' => 2,
                                           'language' => 'All')) >> false ) {
                        $imported++;
                    }
                }
            }
            LogUtil::registerStatus(sprintf(_MH_AUTOLINKUPDATESTATUS, $imported));
        }
    }
    
    MultiHook_import_CensorList();
    
    // Initialisation successful
    return true;
}

/**
 * upgrade the module from an old version
 */
function MultiHook_upgrade($oldversion)
{
    // Upgrade dependent on old version number
    // There is no 
    // break;
    // at the end of each case which means that if you start with eg. version 1.1 all
    // necessary upgrade steps up to the recent version are done.
    // The recent version usually does not appear as a case here. Same for the default case
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
            // collecting the needles is done below on every upgrade  
        case '4.0':
        case '4.5':
            // change the database. DBUtil + ADODB detect the changes on their own
            // and perform all necessary steps without help from the module author
            if (!DBUtil::changeTable('multihook')) {
                return LogUtil::registerError(_MH_UPGRADETO50FAILED);
            }
            MultiHook_import_CensorList();
            break;
    }
    // collecting needles
    // force loading of adminapi
    pnModAPILoad('MultiHook', 'admin', true);
    pnModAPIFunc('MultiHook', 'admin', 'collectneedles');
    // clear compiled templates. This function is new in .8 and ensures that after
    // an upgrade the new templates will be used without the need to manually
    // clear the compiled templates.
    // minor drawback: this clears ALL compiled templates for ALL modules
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
        return LogUtil::registerError(_MH_DBDELETETABLEERROR);
    }

    // Remove module variables
    // using pnModDelVar with only one parameter (the module name) automatically
    // deletes all existing vars in one call
    pnModDelVar('MultiHook');

    // Remove module hooks
    if (!pnModUnregisterHook('item',
                             'transform',
                             'API',
                             'MultiHook',
                             'user',
                             'transform')) {
        return LogUtil::registerError(_MH_COULDNOTUNREGISTER);
    }

    // Deletion successful
    return true;
}

/**
 * import from old censor module
 *
 *
 */
function MultiHook_import_CensorList()
{
    // import Censor list if available
    $censoredwords = pnConfigGetVar('CensorList');
    $censored = 0;
    if(is_array($censoredwords) && count($censoredwords) <> 0) { 
        pnModAPILoad('MultiHook', 'user', true);
        $mhs = pnModAPIFunc('MultiHook', 'user', 'getall', array('filter' => 2));
        // get the short's only
        $short = array();
        if(is_array($mhs)) {
            foreach($mhs as $mh) {
                $short[$mh['short']] = 1;
            }
        }
        foreach($censoredwords as $censoredword) {
            if(!array_key_exists($censoredword, $short)) {
                if( pnModAPIFunc('MultiHook',
                                 'admin',
                                 'create',
                                 array('short' => $censoredword,
                                       'long' => '',
                                       'title' => '',
                                       'type' => 3,
                                       'language' => 'All')) >> false ) {
                    $censored++;
                }
            }
        }
        LogUtil::registerStatus(sprintf(_MH_CENSORUPDATESTATUS, $censored));
    }
}

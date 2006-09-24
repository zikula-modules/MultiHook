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
    // Set up database tables
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $multihooktable = $pntable['multihook'];
    $multihookcolumn = $pntable['multihook_column'];

    $sql = "CREATE TABLE $multihooktable (
            $multihookcolumn[aid] INT(11) NOT NULL auto_increment,
            $multihookcolumn[short] VARCHAR(100) NOT NULL default '',
            $multihookcolumn[long] VARCHAR(200) NOT NULL default '',
            $multihookcolumn[title] VARCHAR(100) NOT NULL default '',
            $multihookcolumn[type] TINYINT(1) NOT NULL,
            $multihookcolumn[language] VARCHAR(30) NOT NULL default '',
            PRIMARY KEY (pn_aid))";
    $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', _MH_DBCREATETABLEERROR);
        return false;
    }

    // Set up module variables
    pnModSetVar('MultiHook', 'itemsperpage', 20);
    pnModSetVar('MultiHook', 'abacfirst', 1);
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
            break;
        case '3.0':
        // collect the needles  
        // force loading of adminapi
        pnModAPILoad('MultiHook', 'admin', true);
        pnModAPIFunc('MultiHook', 'admin', 'collectneedles');
        
    }
    $smarty = new Smarty;
    $smarty->compile_dir = pnConfigGetVar('temp') . '/pnRender_compiled';
    $smarty->cache_dir = pnConfigGetVar('temp') . '/pnRender_cache';
    $smarty->use_sub_dirs = false;
    $smarty->clear_compiled_tpl();

    return true;
}

/**
 * delete the MultiHook module
 */
function MultiHook_delete()
{
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    // remove table
    $sql = "DROP TABLE IF EXISTS ".$pntable['multihook'];
    $dbconn->Execute($sql);
    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', "$sql:".$dbconn->ErrorMsg());
        return false;
    }

    // Remove module hooks
    if (!pnModUnregisterHook('item',
                             'transform',
                             'API',
                             'MultiHook',
                             'user',
                             'transform')) {
        pnSessionSetVar('errormsg', _MH_COULDNOTUNREGISTER);
        return false;
    }

    // Remove module variables
    pnModDelVar('MultiHook');

    // Deletion successful
    return true;
}
?>

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
// Purpose of file:  pnRender plugin
// ----------------------------------------------------------------------

function smarty_function_multihookhelper($params, &$smarty)
{
    if(pnModAvailable('MultiHook')) {
        $modinfo = pnModGetInfo(pnModGetIDFromName('MultiHook'));
        if(version_compare($modinfo['version'], '6.0', '>=')==1) {
            pnModAPIFunc('MultiHook', 'theme', 'preparetheme');
            return pnModFunc('MultiHook', 'theme', 'helper'); 
        }         
    }
}      
?>
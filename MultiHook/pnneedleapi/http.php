<?php
// $Id: pnforum.php 177 2007-09-16 11:00:53Z landseer $
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
// Purpose of file:  MultiHook needle API
// ----------------------------------------------------------------------

/**
 * changeset needle
 * @param $args['nid'] needle id
 * @param $args['needle'] the needle itself
 * @return array()
 */
function MultiHook_needleapi_http($args)
{
    // simple replacement, no need to cache anything
    if (isset($args['nid']) && !empty($args['nid'])) {
        $url = DataUtil::formatForDisplay($args['needle'] . $args['nid']);

        if(stristr(pnGetBaseURL(), $url) === false) {
            $externallinkclass =pnModGetVar('MultiHook', 'externallinkclass', '');
            if(!empty($externallinkclass)) {
                $extclass = "class=\"$externallinkclass\"";
            }
        } else {
            $extclass = '';
        }

        $result = '<a ' . $extclass . ' href="' . $url . '">' . $url . '</a>'; 
        return $result;
    } 
    return $args['nid'];   
}

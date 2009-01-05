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
 * @return array()
 */
function MultiHook_needleapi_changeset($args)
{
    pnModLangLoad('MultiHook', 'trac');
    // simple replacement, no need to cache anything
    if (isset($args['nid']) && !empty($args['nid'])) {
        if (substr($args['nid'], 0, 1) != '-') {
            $args['nid'] =  '-' . $args['nid'];
        }
        $parts = explode('-', $args['nid']);
        $project = DataUtil::formatForDisplay(strtolower($parts[1]));
        $changeset = (int)DataUtil::formatForDisplay($parts[2]);
        $displayproject = (strtolower($project) == 'core') ? 'Zikula' : $project;
        $result = '<a href="http://code.zikula.org/' . $project . '/changeset/' . $changeset . '" title="' . pnML('_MH_TRAC_CHANGESETLINKTITLE', array('changeset' => $changeset, 'project' => $displayproject )) . '">' . pnML('_MH_TRAC_CHANGESETLINKNAME', array('changeset' => $changeset, 'project' => $displayproject )) . '</a>'; 
    } else {
        $result = _MH_TRAC_NONEEDLEID;
    } 
    return $result;   
}

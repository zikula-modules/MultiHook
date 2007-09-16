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
// Purpose of file:  Table information for MultiHook module
// ----------------------------------------------------------------------

function MultiHook_pntables()
{
    // Initialise table array
    $pntable = array();


    $multihook = DBUtil::getLimitedTablename('multihook') ;
    $pntable['multihook'] = $multihook;
    $pntable['multihook_column'] = array('aid'      => 'pn_aid',
                                         'short'    => 'pn_short',
                                         'long'     => 'pn_long',
                                         'title'    => 'pn_title',
                                         'type'     => 'pn_type',
                                         'language' => 'pn_language');

    // column definitions
    $pntable['multihook_column_def'] = array('aid'      => "I AUTO PRIMARY",
                                             'short'    => "C(100) NOTNULL DEFAULT ''",
                                             'long'     => "C(200) NOTNULL DEFAULT ''",
                                             'title'    => "C(100) NOTNULL DEFAULT ''",
                                             'type'     => "I1 NOTNULL DEFAULT 0",
                                             'language' => "C(100) NOTNULL DEFAULT ''");

    // addtitional indexes
    $pntable['multihook_column_idx'] = array ('short' => 'short',
                                              'type'  => 'type');

    // Return the table information
    return $pntable;
}

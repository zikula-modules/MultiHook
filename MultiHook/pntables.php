<?php
// $Id$
// ----------------------------------------------------------------------
// PostNuke Content Management System
// Copyright (C) 2002 by the PostNuke Development Team.
// http://www.postnuke.com/
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
// Original Author of file: Jim McDonald
// Purpose of file:  Table information for MultiHook module
// ----------------------------------------------------------------------

function MultiHook_pntables()
{
    // Initialise table array
    $pntable = array();

    // Get the name for the MultiHook item table
    $multihook = pnConfigGetVar('prefix') . '_multihook';

    // Set the table name
    $pntable['multihook'] = $multihook;

    // Set the column names
    $pntable['multihook_column'] = array('aid'      => $multihook . '.pn_aid',
                                         'short'    => $multihook . '.pn_short',
                                         'long'     => $multihook . '.pn_long',
                                         'title'    => $multihook . '.pn_title',
                                         'type'     => $multihook . '.pn_type',
                                         'language' => $multihook . '.pn_language');

    // Return the table information
    return $pntable;
}

?>
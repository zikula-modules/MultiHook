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
// Purpose of file:  MultiHook version information
// ----------------------------------------------------------------------

// the defines used here are defined in pnlang/xxx/version.php

$dom = ZLanguage::getModuleDomain('MultiHook');

$modversion['name'] = __('MultiHook', $dom);
$modversion['version'] = '5.2';
$modversion['description'] = __('Creates xhtml tags for abbreviations, acronyms and autolinks, while providing specific content censorship.', $dom);
$modversion['displayname'] = __('MultiHook', $dom);
//! module url should be lowercase without spaces and different to displayname
$modversion['url'] = __('multihook', $dom);
$modversion['credits'] = 'pndocs/credits.txt';
$modversion['help'] = 'pndocs/help.txt';
$modversion['changelog'] = 'pndocs/changelog.txt';
$modversion['license'] = 'pndocs/license.txt';
$modversion['official'] = 0;
$modversion['author'] = 'Frank Schummertz';
$modversion['contact'] = 'frank.schummertz@landseer-stuttgart.de';
$modversion['admin'] = 1;
$modversion['user'] = 1;
$modversion['securityschema'] = array('MultiHook::' => 'Shorttext::$ID' );

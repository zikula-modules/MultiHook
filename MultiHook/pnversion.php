<?php
/**
 * Multihook
 *
 * @copyright (c) 2001-now, Multihook Development Team
 * @link http://code.zikula.org/multihook
 * @version $Id$
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Multihook
 */

$dom = ZLanguage::getModuleDomain('MultiHook');

$modversion['name']             = 'MultiHook';
$modversion['version']          = '5.2';
$modversion['description']      = __('Creates xhtml tags for abbreviations, acronyms and autolinks, while providing specific content censorship.', $dom);
$modversion['displayname']      = __('MultiHook', $dom);
//! module url should be lowercase without spaces and different to displayname
$modversion['url']              = __('multihook', $dom);
$modversion['credits']          = 'pndocs/credits.txt';
$modversion['help']             = 'pndocs/help.txt';
$modversion['changelog']        = 'pndocs/changelog.txt';
$modversion['license']          = 'pndocs/license.txt';
$modversion['official']         = 0;
$modversion['author']           = 'Frank Schummertz';
$modversion['contact']          = 'frank.schummertz@landseer-stuttgart.de';
$modversion['admin']            = 1;
$modversion['user']             = 1;
$modversion['securityschema']   = array('MultiHook::' => 'Shorttext::$ID' );

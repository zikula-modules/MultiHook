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

/**
 * pnRender plugin
 *
 * This file is a plugin for pnRender, the Zikula implementation of Smarty
 *
 * @package      Xanthia_Templating_Environment
 * @subpackage   pnRender
 * @version      $Id$
 * @author       The Zikula development team
 * @link         http://www.zikula.org  The Zikula Home Page
 * @copyright    Copyright (C) 2002 by the Zikula Development Team
 * @license      http://www.gnu.org/copyleft/gpl.html GNU General Public License
 */


/**
 * Smarty function to display a drop down list of languages
 *
 * Available parameters:
 *   - assign:   If set, the results are assigned to the corresponding variable instead of printed out
 *   - name:     Name for the control
 *   - selected: Selected value
 *   - installed: if set only show languages existing in languages folder
 *
 * Example
 *   <!--[languagelist name=language selected=eng]-->
 *
 *
 * @author       Mark West, Frank Schummertz
 * @since        25 April 2004
 * @param        array       $params      All attributes passed to this function from the template
 * @param        object      &$smarty     Reference to the Smarty object
 * @return       string      the value of the last status message posted, or void if no status message exists
 */
function smarty_function_mhlanguagelist($params, &$smarty)
{
    extract($params);

    unset($params['name']);
    unset($params['id']);
    unset($params['selected']);
    unset($params['all']);
   	unset($params['installed']);// itevo, MNA: added param to show only installed languages in pulldown

    if (!isset($name)) {
        $smarty->trigger_error("languagelist:  parameter 'name' required");
        return false;
    }

    $id = (isset($id)) ? $id : $name;

    if (!isset($all)) {
        $all = true;
    }

    $languagedropdown = '<select id="' . DataUtil::formatForDisplay($id) . '" name="'.DataUtil::formatForDisplay($name)."\">\n";
    if ($all) {
        $languagedropdown .= '<option value="All">'.DataUtil::formatForDisplay(_ALL)."</option>\n";
    }

    $languagelist = LanguageUtil::getInstalledLanguages();
    foreach ($languagelist as $code => $text) {
        if (isset($selected) && $code == $selected) {
            $selectedtext = ' selected="selected"';
        } else {
            $selectedtext = '';
        }
        $languagedropdown .= '<option value="'.DataUtil::formatForDisplay($code)."\"$selectedtext>".DataUtil::formatForDisplay($text)."</option>\n";
    }
    $languagedropdown .= '</select>';

    if (isset($assign)) {
        $smarty->assign($assign, $languagedropdown);
    } else {
        return $languagedropdown;
    }
}

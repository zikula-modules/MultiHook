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
// Purpose of file:  MultiHook user API
// ----------------------------------------------------------------------

/**
 * get all entries
 *@params $arg['filter'] int 0=abbr, 1=acronyms, 2=links
 * @returns array
 * @return array of entries, or false on failure
 */
function MultiHook_userapi_getall($args)
{
    extract($args);

    // Optional arguments
    if (!isset($startnum) || !is_numeric($startnum)) {
        $startnum = 1;
    }
    if (!isset($numitems) || !is_numeric($numitems)) {
        $numitems = -1;
    }

    if (!pnSecAuthAction(0, 'MultiHook::', '::', ACCESS_READ)) {
        return false;
    }
    $abacs = array();

    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $multihooktable = $pntable['multihook'];
    $multihookcolumn = $pntable['multihook_column'];

    $where = "";
    if($filter<>-99) {
        $where = "WHERE $multihookcolumn[type]=$filter";
    }
    $sql = "SELECT $multihookcolumn[aid],
                   $multihookcolumn[short],
                   $multihookcolumn[long],
                   $multihookcolumn[title],
                   $multihookcolumn[type],
                   $multihookcolumn[language]
            FROM $multihooktable
            $where
            ORDER BY $multihookcolumn[short]";

    $result = $dbconn->SelectLimit($sql, (int)$numitems, (int)$startnum-1);

    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', _MH_SELECTFAILED);
        return false;
    }

    for (; !$result->EOF; $result->MoveNext()) {
        list($aid, $short, $long, $title, $type, $language) = $result->fields;
        if (pnSecAuthAction(0, 'MultiHook::', "$short::$aid", ACCESS_READ)) {
            $abacs[] = array('aid' => $aid,
                             'short' => $short,
                             'long' => $long,
                             'title' => $title,
                             'type' => $type,
                             'language' => $language);
        }
    }

    $result->Close();
    return $abacs;
}

/**
 * get a specific entry
 * @poaram $args['aid'] id of item to get
 * @returns array
 * @return abac array, or false on failure
 */
function MultiHook_userapi_get($args)
{
    extract($args);

    if (!isset($aid) || !is_numeric($aid)) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return false;
    }

    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $multihooktable = $pntable['multihook'];
    $multihookcolumn = $pntable['multihook_column'];

    $sql = "SELECT $multihookcolumn[aid],
                   $multihookcolumn[short],
                   $multihookcolumn[long],
                   $multihookcolumn[title],
                   $multihookcolumn[type],
                   $multihookcolumn[language]
            FROM $multihooktable
            WHERE $multihookcolumn[aid] = '".(int)pnVarPrepForStore($aid)."'";
    $result =& $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', _MH_SELECTFAILED);
        return false;
    }

    list($aid, $short, $long, $title, $type, $language) = $result->fields;
    $result->Close();

    if (!pnSecAuthAction(0, 'MultiHook::', "$short::$aid", ACCESS_READ)) {
        return false;
    }
    $abac = array('aid' => $aid,
                  'short' => $short,
                  'long' => $long,
                  'title' => $title,
                  'type' => $type,
                  'language' => $language);

    return $abac;
}

/**
 * count the number of items in the database
 * @params $args['filter'] int 0=abbr, 1=acronyms, 2=links
 * @returns integer
 * @returns number of items in the database
 */
function MultiHook_userapi_countitems($args)
{
    extract($args);
    unset($args);
    
    if(!isset($filter)) {
        return false;
    }
    
    $dbconn =& pnDBGetConn(true);
    $pntable =& pnDBGetTables();

    $multihooktable = $pntable['multihook'];
    $multihookcolumn = $pntable['multihook_column'];

    $sql = "SELECT COUNT(1)
            FROM $multihooktable
            WHERE $multihookcolumn[type]=$filter";
    $result =& $dbconn->Execute($sql);

    if ($dbconn->ErrorNo() != 0) {
        return false;
    }

    list($numitems) = $result->fields;

    $result->Close();
    return $numitems;
}

/**
 * transform text
 * @param $args['extrainfo'] string or array of text items
 * @returns string
 * @return string or array of transformed text items
 */
function MultiHook_userapi_transform($args)
{
    // Get arguments from argument array
    extract($args);
    // Argument check
    if (!isset($extrainfo)) {
        pnSessionSetVar('errormsg', _MODARGSERROR);
        return;
    }

    if (is_array($extrainfo)) {
        $transformed = array();
        foreach($extrainfo as $text) {
            $transformed[] = MultiHook_userapitransform($text);
        }
    } else {
        $transformed = MultiHook_userapitransform($text);
    }

    return $transformed;
}


function MultiHook_userapitransform($text)
{
    static $search = array();
    static $replace = array();
    static $gotabbreviations = 0;

    $onlyonce = (pnModGetVar('MultiHook', 'abacfirst')==1) ? true : false;
    $externallinkclass =pnModGetVar('MultiHook', 'externallinkclass');

    // Step 1 - move all URLs out of the text and replace them with placeholders
    // thanks to www.regexlib.com for the regular expression
    preg_match_all("/((mailto\:|(news|(ht|f)tp(s?))\:\/\/){1}\S+)/i", $text, $urls);
    $urlnum = count($urls[1]);
    for ($i = 0; $i <$urlnum; $i++) {
        $text = preg_replace('/(' . preg_quote($urls[1][$i], '/') . ')/', " MULTIHOOKURLREPLACEMENT{$i} ", $text, 1);
    }

    // Step 2 - move all tags out of the text and replace them with placeholders
    preg_match_all('/(<a\s+.*?\/a>|<[^>]+>)/i', $text, $matches);
    $matchnum = count($matches[1]);
    for ($i = 0; $i <$matchnum; $i++) {
        $text = preg_replace('/(' . preg_quote($matches[1][$i], '/') . ')/', " MULTIHOOKTAGREPLACEMENT{$i} ", $text, 1);
    }

    if (empty($gotabbreviations)) {
        $gotabbreviations = 1;
        $thislang = pnUserGetLang();
        pnModAPILoad('MultiHook', 'user');
        $tmps = pnModAPIFunc('MultiHook', 'user', 'getall', array('filter' => -99));
        // Create search/replace array from abbreviations/links information
        foreach ($tmps as $tmp) {
            if($tmp['language']==$thislang || $tmp['language']=="") {
//                $search[] = '/(?<![\w@\.:-])(' . preg_quote($tmp['short'], '/'). ')(?![\w@:-])(?!\.\w)/i';
                $extclass = (preg_match("/(^http:\/\/)/", $tmp['long'])==1) ? "class=\"$externallinkclass\"" : "";
                $tmp['long']  = preg_replace('/(\b)/', '\\1MULTIHOOKTEMPORARY', $tmp['long']);
                $tmp['title'] = preg_replace('/(\b)/', '\\1MULTIHOOKTEMPORARY', $tmp['title']);
                if($tmp['type']==0) {
                    // 0 = Abbreviation 
                    $search[] = '/(?<![\/\w@\.:])(' . preg_quote($tmp['short'], '/'). ')(?![\/\w@:])(?!\.\w)/i';
                    $replace[] = '<abbr title="' . htmlspecialchars($tmp['long']) . '"><span class="abbr" title="'. htmlspecialchars($tmp['long']) .'">' . htmlspecialchars($tmp['short']) . '</span></abbr>';
                } else if($tmp['type']==1) { 
                    // 1 = Acronym
                    $search[] = '/(?<![\/\w@\.:])(' . preg_quote($tmp['short'], '/'). ')(?![\/\w@:])(?!\.\w)/i';
                    $replace[] = '<acronym title="' . htmlspecialchars($tmp['long']) . '">' . htmlspecialchars($tmp['short']) . '</acronym>';
                } else if($tmp['type']==2) { 
                    // 2 = Link
                    $search[] = '/(?<![\/\w@\.:-])(' . preg_quote($tmp['short'], '/'). ')(?![\/\w@:-])(?!\.\w)/i';
                    $replace[] = '<a '.$extclass.' href="' . htmlspecialchars($tmp['long']) . '" title="' . htmlspecialchars($tmp['title']) . '">' . htmlspecialchars($tmp['short']) . '</a>';
                }
            }
        }
    }

    // Step 3 - the main replacements
    if($onlyonce==true) {
        $text = preg_replace($search, $replace, $text, 1);
    } else {
        $text = preg_replace($search, $replace, $text);
    }

    // Step 4 - replace the spaces we munged in preparation of step 3
    $text = preg_replace('/MULTIHOOKTEMPORARY/', '', $text);

    // Step 5 - replace the HTML tags that we removed in step 2
    for ($i = 0; $i <$matchnum; $i++) {
        $text = preg_replace("/ MULTIHOOKTAGREPLACEMENT{$i} /", $matches[1][$i], $text, 1);
    }

    // Step 6 - replace the URLs that we removed in step 1
    for ($i = 0; $i <$urlnum; $i++) {
        $text = preg_replace("/ MULTIHOOKURLREPLACEMENT{$i} /", $urls[1][$i], $text, 1);
    }

    return $text;
}

?>
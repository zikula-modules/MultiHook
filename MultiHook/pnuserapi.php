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
 * @params $args['filter'] int 0=abbr, 1=acronyms, 2=links
 * @params $args['sortbylength'] bool 
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
    if(isset($filter) && is_numeric($filter) && ($filter>=0 && $filter<=2)) {
        $where = "WHERE $multihookcolumn[type]=" . pnVarPrepForStore($filter);
    }
    
    if(isset($sortbylength) && $sortbylength==true) {
        $orderby = "ORDER BY LENGTH($multihookcolumn[short]) DESC";
    } else {
        $orderby = "ORDER BY $multihookcolumn[short]";
    }
    $sql = "SELECT $multihookcolumn[aid],
                   $multihookcolumn[short],
                   $multihookcolumn[long],
                   $multihookcolumn[title],
                   $multihookcolumn[type],
                   $multihookcolumn[language]
            FROM $multihooktable
            $where
            $orderby"; // ORDER BY LENGTH($multihookcolumn[short]) DESC"; //$multihookcolumn[short]";

    $result = $dbconn->SelectLimit($sql, (int)$numitems, (int)$startnum-1);

    if ($dbconn->ErrorNo() != 0) {
        pnSessionSetVar('errormsg', _MH_SELECTFAILED);
        return false;
    }

    for (; !$result->EOF; $result->MoveNext()) {
        list($aid, $short, $long, $title, $type, $language) = $result->fields;
        if (pnSecAuthAction(0, 'MultiHook::', "$short::$aid", ACCESS_READ)) {
            $abacs[] = array('aid' => $aid,
                             'short' => trim($short),
                             'long' => trim($long),
                             'title' => trim($title),
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
                  'short' => trim($short),
                  'long' => trim($long),
                  'title' => trim($title),
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
            WHERE $multihookcolumn[type]=" . pnVarPrepForStore($filter);
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
    if(strlen($text) == 0) {
        return $text;
    }
    
    // check the user agent - if it is a bot, return immediately
    $robotslist = array ( "ia_archiver",
                          "googlebot",
                          "mediapartners-google",
                          "yahoo!",
                          "msnbot",
                          "jeeves",
                          "lycos");
    $useragent = pnServerGetVar('HTTP_USER_AGENT');
    for($cnt=0; $cnt < count($robotslist); $cnt++) {
        if(strpos(strtolower($useragent), $robotslist[$cnt]) !== false) {
            return $text;
        }
    }

    static $search = array();
    static $replace = array();
    static $finalsearch = array();
    static $finalreplace = array();
    static $gotabbreviations = 0;
    static $mhadmin = false;
    static $mhshoweditlink = false;
    static $schemes = array('http', 'https', 'ftp', 'gopher', 'ed2k', 'news', 'mailto', 'telnet');
    static $imageinfo;

    $onlyonce = (pnModGetVar('MultiHook', 'abacfirst')==1) ? true : false;
    $externallinkclass =pnModGetVar('MultiHook', 'externallinkclass');
    $mhincodetags = (pnModGetVar('MultiHook', 'mhincodetags')==1) ? true : false;
    $mhlinktitle = (pnModGetVar('MultiHook', 'mhlinktitle')==1) ? true : false;
    $mhreplaceabbr = (pnModGetVar('MultiHook', 'mhreplaceabbr')==1) ? true : false;
    $haveoverlib = pnModAvailable('overlib');
    $mhadmin = pnSecAuthAction(0, 'MultiHook::', '.*', ACCESS_ADMIN);
    $mhshoweditlink = (pnModGetVar('MultiHook', 'mhshoweditlink')==1) ? true : false;

    // Step 0 - move all bbcode with [code][/code] out of the way
    //          if MultiHook is configured accordingly
    if($mhincodetags==false) {
        // if we are faster than pn_bbcode, we will have to remove the code tags
        $codecount1 = preg_match_all("/\[code(.*)\](.*)\[\/code\]/si", $text, $codes1);
        for($i=0; $i < $codecount1; $i++) {
            $text = preg_replace('/(' . preg_quote($codes1[0][$i], '/') . ')/', " MULTIHOOKCODE1REPLACEMENT{$i} ", $text, 1);
        }
        // but pn_bbode may have been faster than we are,. To avoid any problems its embraces the
        // replaced code tags with <!--code--> and <!--/code-->
        // this is what we are taking care of now
        $codecount2 = preg_match_all("/<!--code-->(.*)<!--\/code-->/si", $text, $codes2);
        for($i=0; $i < $codecount2; $i++) {
            $text = preg_replace('/(' . preg_quote($codes2[0][$i], '/') . ')/', " MULTIHOOKCODE2REPLACEMENT{$i} ", $text, 1);
        }
    }

    // Step 1 - move all links out of the text and replace them with placeholders
    $tagcount = preg_match_all('/<a(.*)>(.*)<\/a>/si', $text, $tags);
    for ($i = 0; $i < $tagcount; $i++) {
        $text = preg_replace('/(' . preg_quote($tags[0][$i], '/') . ')/', " MULTIHOOKTAGREPLACEMENT{$i} ", $text, 1);
    }

    // Step 2 - remove all html tags, we do not want to change them!!
    $htmlcount = preg_match_all("/<(?:[^\"\']+?|.+?(?:\"|\').*?(?:\"|\')?.*?)*?>/si", $text, $html);
    for ($i=0; $i < $htmlcount; $i++) {
        $text = preg_replace('/(' . preg_quote($html[0][$i], '/') . ')/', " MULTIHOOKHTMLREPLACEMENT{$i} ", $text, 1);
    }

    // Step 3 - move all bbcode with [url][/url] out of the way
    $urlcount = preg_match_all("#\[url(.*)\](.*)\[\/url\]#si", $text, $urls);
    for($i=0; $i < $urlcount; $i++) {
        $text = preg_replace('/(' . preg_quote($urls[0][$i], '/') . ')/', " MULTIHOOKURLREPLACEMENT{$i} ", $text, 1);
    }

    // Step 4 - move all urls starting with http:// etc. out of the way
    $linkcount = preg_match_all("/(http|https|ftp|ftps|news)\:\/\/([a-zA-Z0-9\-\._]+[\.]{1}[a-zA-Z]{2,6})(\/[a-zA-Z0-9\-\.\?\,\'\/\\\+&%\$#_=~]+)?/si", $text, $links);
    for($i=0; $i < $linkcount; $i++) {
        $text = preg_replace('/(' . preg_quote($links[0][$i], '/') . ')/', " MULTIHOOKLINKREPLACEMENT{$i} ", $text, 1);
    }

    // Step 5 - move hilite hook additions out of the text
    $hilitecount = preg_match_all("/<!--hilite-->(.*)<!--\/hilite-->/si", $text, $hilite);
    for($i=0; $i < $hilitecount; $i++) {
        $text = preg_replace('/(' . preg_quote($hilite[0][$i], '/') . ')/', " MULTIHOOKHILITEREPLACEMENT{$i} ", $text, 1);
    }

    $overlib_border = 1;
    $overlib_font   = 'arial';
    $overlib_cpfont   = 'arial';
    $overlib_fontsize = '10px';
    $overlib_cpfontsize = '10px';
    $overlib_fontcolor = '#ffffff';
    $overlib_cpfontcolor = '#000000';
    $overlib_fgcolor = '#000000';
    $overlib_bgcolor = '#ffffff';
    $overlib_parameters = "TEXTFONT, '$overlib_font', CAPTIONFONT, '$overlib_cpfont', TEXTSIZE, '$overlib_fontsize', CAPTIONSIZE, '$overlib_cpfontsize', BORDER, $overlib_border, TEXTCOLOR, '$overlib_fontcolor', CAPCOLOR, '$overlib_cpfontcolor', BGCOLOR, '$overlib_bgcolor', FGCOLOR, '$overlib_fgcolor'";

    if (empty($gotabbreviations)) {
        $gotabbreviations = 1;
        $tmps = pnModAPIFunc('MultiHook', 'user', 'getall', array('sortbylength' => true));
        // Create search/replace array from abbreviations/links information
        foreach ($tmps as $tmp) {
            $extclass = (preg_match("/(^http:\/\/)/", $tmp['long'])==1) ? "class=\"$externallinkclass\"" : "";
            // check if the current tmp is a link
            if($tmp['type']==2) {
                // make sure that relative urls get converted to absolute urls (safehtml needs this)
                $exploded_url = explode(':', $tmp['long']);
                if(!in_array($exploded_url[0], $schemes)) {
                    // url does not start with one of the schemes defined above - we consider it
                    // being a relative path now
                    // next check for leading / in  relative url
                    if($tmp['long'][0] == '/') {
                        // and remove it
                        $tmp['long'] = substr($tmp['long'], 1);
                    }
                    $tmp['long'] = pnGetBaseURL() . $tmp['long'];
                }
            }
            
            $tmp['long']  = preg_replace('/(\b)/', '\\1MULTIHOOKTEMPORARY', $tmp['long']);
            $tmp['title'] = preg_replace('/(\b)/', '\\1MULTIHOOKTEMPORARY', $tmp['title']);
            $xhtmllang = get_xhtml_language($tmp['language']);

            if($tmp['type']==0) {
                // 0 = Abbreviation
                $search_temp = '/(?<![\/\w@\.:])(' . preg_quote($tmp['short'], '/'). ')(?![\/\w@:])(?!\.\w)/i';
                $search[]      = $search_temp;
                $replace[]     = md5($search_temp);
                $finalsearch[] = '/' . preg_quote(md5($search_temp), '/') . '/';
                if($mhreplaceabbr==false) {
                    if($haveoverlib) {
                        $replace_temp = '<abbr '.$xhtmllang.' onmouseover="return overlib(\'' . pnVarPrepForDisplay($tmp['long']) . '\', CAPTION, \'' . pnVarPrepForDisplay(_MH_ABBREVIATION) . ': '. pnVarPrepForDisplay($tmp['short']) .'\', ' . $overlib_parameters . ')" onmouseout="return nd();"><span class="abbr" onmouseover="return overlib(\'' . pnVarPrepForDisplay($tmp['long']) . '\', CAPTION, \'' . pnVarPrepForDisplay(_MH_ABBREVIATION) . ': '. pnVarPrepForDisplay($tmp['short']) .'\')" onmouseout="return nd();">' . pnVarPrepForDisplay($tmp['short']) . '</span></abbr>';
                    } else {
                        $replace_temp = '<abbr '.$xhtmllang.' title="' . pnVarPrepForDisplay($tmp['long']) . '"><span class="abbr" title="'. pnVarPrepForDisplay($tmp['long']) .'">' . pnVarPrepForDisplay($tmp['short']) . '</span></abbr>';
                    }

                } else {
                    $replace_temp = pnVarPrepForDisplay($tmp['long']);
                }
                if($mhadmin == true && $mhshoweditlink==true) {
                    $replace_temp .= '<a title="' . pnVarPrepForDisplay(_EDIT) . ': ' . $tmp['short'] . ' (' . pnVarPrepForDisplay(_MH_ABBREVIATION) . ')" href="' . pnModURL('MultiHook', 'admin', 'edit',array('aid' => $tmp['aid'])) . '">+</a>';
                }
                $finalreplace[] = $replace_temp;
                unset($search_temp);
                unset($replace_temp);
            } else if($tmp['type']==1) {
                // 1 = Acronym
                $search_temp = '/(?<![\/\w@\.:])(' . preg_quote($tmp['short'], '/'). ')(?![\/\w@:])(?!\.\w)/i';
                $search[]      = $search_temp;
                $replace[]     = md5($search_temp);
                $finalsearch[] = '/' . preg_quote(md5($search_temp), '/') . '/';
                if($haveoverlib) {
                    $replace_temp = '<acronym '.$xhtmllang.' onmouseover="return overlib(\'' . pnVarPrepForDisplay($tmp['long']) . '\', CAPTION, \'' . pnVarPrepForDisplay(_MH_ACRONYM) . ': '. pnVarPrepForDisplay($tmp['short']) .'\', ' . $overlib_parameters . ')" onmouseout="return nd();">' . pnVarPrepForDisplay($tmp['short']) . '</acronym>';
                } else {
                    $replace_temp = '<acronym '.$xhtmllang.' title="' . pnVarPrepForDisplay($tmp['long']) . '">' . pnVarPrepForDisplay($tmp['short']) . '</acronym>';
                }
                if($mhadmin == true && $mhshoweditlink==true) {
                    $replace_temp .= '<a title="' . pnVarPrepForDisplay(_EDIT) . ': ' . $tmp['short'] . ' (' . pnVarPrepForDisplay(_MH_ACRONYM) . ')" href="' . pnModURL('MultiHook', 'admin', 'edit',array('aid' => $tmp['aid'])) . '">+</a>';
                }
                $finalreplace[] = $replace_temp;
                unset($search_temp);
                unset($replace_temp);
            } else if($tmp['type']==2) {
                // 2 = Link
                // if short beginns with a single ' we need another regexp to not check for \w
                // this enables autolinks for german deppenapostrophs :-)
                if($tmp['short'][0] == '\'') {
                    $search_temp = '/(?<![\/@\.:-])(' . preg_quote($tmp['short'], '/'). ')(?![\/\w@:-])(?!\.\w)/i';
                } else {
                    $search_temp = '/(?<![\/\w@\.:-])(' . preg_quote($tmp['short'], '/'). ')(?![\/\w@:-])(?!\.\w)/i';
                }
                $search[]      = $search_temp;
                $replace[]     = md5($search_temp);
                $finalsearch[] = '/' . preg_quote(md5($search_temp), '/') . '/';
                // prepare url
                $absurl = pnVarPrepForDisplay($tmp['long']);

                if($mhlinktitle==false) {
                    if($haveoverlib) {
                        $replace_temp = '<a '.$extclass.' href="' . $absurl . '" title="" onmouseover="return overlib(\'' . $absurl . '\', CAPTION, \''. pnVarPrepForDisplay($tmp['title']) .'\', ' . $overlib_parameters . ')" onmouseout="return nd();">' . pnVarPrepForDisplay($tmp['short']) . '</a>';
                    } else {
                        $replace_temp = '<a '.$extclass.' href="' . $absurl . '" title="' . pnVarPrepForDisplay($tmp['title']) . '">' . pnVarPrepForDisplay($tmp['short']) . '</a>';
                    }
                } else {
                    if($haveoverlib) {
                        $replace_temp = '<a '.$extclass.' href="' . $absurl . '" title="" onmouseover="return overlib(\'' . $absurl . '\', CAPTION, \''. pnVarPrepForDisplay($tmp['title']) .'\', ' . $overlib_parameters . ')" onmouseout="return nd();">' . pnVarPrepForDisplay($tmp['title']) . '</a>';
                    } else {
                        $replace_temp = '<a '.$extclass.' href="' . $absurl . '" title="' . pnVarPrepForDisplay($tmp['title']) . '">' . pnVarPrepForDisplay($tmp['short']) . '</a>';
                    }
                }
                if($mhadmin == true && $mhshoweditlink==true) {
                    $replace_temp .= '<a title="' . pnVarPrepForDisplay(_EDIT) . ': ' . $tmp['short'] . ' (' . pnVarPrepForDisplay(_MH_LINK) . ')" href="' . pnModURL('MultiHook', 'admin', 'edit',array('aid' => $tmp['aid'])) . '">+</a>';
                }
                $finalreplace[] = $replace_temp;
                unset($search_temp);
                unset($replace_temp);
            }
            
        }
    }

    // Step 6 - the main replacements
    if($onlyonce==true) {
        $text = preg_replace($search, $replace, $text, 1);
        $text = preg_replace($finalsearch, $finalreplace, $text, 1);
    } else {
        $text = preg_replace($search, $replace, $text);
        $text = preg_replace($finalsearch, $finalreplace, $text);
    }

    // Step 7 - replace the spaces we munged in preparation of step 6
    $text = str_replace('MULTIHOOKTEMPORARY', '', $text);

    // Step 8-12 - replace the tags that we removed before
    for ($i = 0; $i < $hilitecount; $i++) {
        $text = preg_replace("/ MULTIHOOKHILITEREPLACEMENT{$i} /", $hilite[0][$i], $text, 1);
    }

    for ($i = 0; $i < $linkcount; $i++) {
        $text = preg_replace("/ MULTIHOOKLINKREPLACEMENT{$i} /", $links[0][$i], $text, 1);
    }
    for ($i = 0; $i < $urlcount; $i++) {
        $text = preg_replace("/ MULTIHOOKURLREPLACEMENT{$i} /", $urls[0][$i], $text, 1);
    }
    for ($i = 0; $i < $htmlcount; $i++) {
        $text = preg_replace("/ MULTIHOOKHTMLREPLACEMENT{$i} /", $html[0][$i], $text, 1);
    }

    for ($i = 0; $i < $tagcount; $i++) {
        $text = preg_replace("/ MULTIHOOKTAGREPLACEMENT{$i} /", $tags[0][$i], $text, 1);
    }

    if($mhincodetags==false) {
        for ($i = 0; $i < $codecount2; $i++) {
            $text = preg_replace("/ MULTIHOOKCODE2REPLACEMENT{$i} /", $codes2[0][$i], $text, 1);
        }
        for ($i = 0; $i < $codecount1; $i++) {
            $text = preg_replace("/ MULTIHOOKCODE1REPLACEMENT{$i} /", $codes1[0][$i], $text, 1);
        }
    }

    return $text;
}

function get_xhtml_language($lang)
{
    $alllanguages = array( "ara" => "ar",
                           "bul" => "bg",
                           "zho" => "zh",
                           "cat" => "ca",
                           "ces" => "cs",
                           "hrv" => "hr",
                           "cro" => "hr",
                           "dan" => "da",
                           "nld" => "nl",
                           "eng" => "en",
                           "epo" => "eo",
                           "est" => "et",
                           "fin" => "fi",
                           "fra" => "fr",
                           "deu" => "de",
                           "ell" => "el",
                           "heb" => "he",
                           "hun" => "hu",
                           "isl" => "is",
                           "ind" => "id",
                           "ita" => "it",
                           "jpn" => "ja",
                           "kor" => "ko",
                           "lav" => "lv",
                           "lit" => "lt",
                           "mas" => "ml",
                           "mkd" => "mk",
                           "nor" => "no",
                           "pol" => "pl",
                           "por" => "pt",
                           "ron" => "ro",
                           "rus" => "ru",
                           "slv" => "sl",
                           "spa" => "es",
                           "swe" => "sv",
                           "tha" => "th",
                           "tur" => "tk",
                           "ukr" => "uk",
                           "yid" => "yi");
    $lang = (empty($lang)) ? pnUserGetLang() : $lang;
    $shortlang = $alllanguages[$lang];
    if(!empty($shortlang)) {
        return 'lang="' . $shortlang . '" xml:lang="' . $shortlang . '"';
    }
    return '';
}


?>
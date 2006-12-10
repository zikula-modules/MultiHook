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
// Purpose of file:  MultiHook userapi functions
// ----------------------------------------------------------------------

include_once('modules/MultiHook/common.php');

/**
 * get all entries
 * @params $args['filter'] int 0=abbr, 1=acronyms, 2=links, 3=censored words
 * @params $args['sortbylength'] bool
 * @params $args['startnum'] int
 * @params $args['numitens'] int
 * @returns array
 * @return array of entries, or false on failure
 */
function MultiHook_userapi_getall($args)
{
    // Optional arguments
    if (!isset($args['startnum']) || !is_numeric($args['startnum'])) {
        $args['startnum'] = 0;
    }
    if (!isset($args['numitems']) || !is_numeric($args['numitems'])) {
        $args['numitems'] = -1;
    }

    if (!SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_READ)) {
        return LogUtil::registerError(_MH_NOAUTH);
    }

    $permfilter[] = array ('realm'            =>  0,
                           'component_left'   =>  'MultiHook',
                           'component_middle' =>  '',
                           'component_right'  =>  '',
                           'instance_left'    =>  'short',
                           'instance_middle'  =>  '',
                           'instance_right'   =>  'aid',
                           'level'            =>  ACCESS_READ);

    pnModDBInfoLoad('MultiHook', 'MultiHook');
    $pntable =& pnDBGetTables();
    $multihookcolumn = $pntable['multihook_column'];

    $where = '';
    if(isset($args['filter']) && is_numeric($args['filter']) && ($args['filter']>=0 && $args['filter']<=3)) {
        $where = "WHERE $multihookcolumn[type]=" . DataUtil::formatForStore($args['filter']);
    }

    if(isset($args['sortbylength']) && $args['sortbylength']==true) {
        $orderby = "ORDER BY LENGTH($multihookcolumn[short]) DESC";
    } else {
        $orderby = "ORDER BY $multihookcolumn[short]";
    }

    $abacs = DBUtil::selectObjectArray('multihook', $where, $orderby, (int)$args['startnum'], (int)$args['numitems'], '', $permfilter);
    if ($abacs === false) {
        return LogUtil::registerError(_MH_SELECTFAILED);
    }
    return $abacs;
}

/**
 * get a specific entry
 * @param $args['aid'] id of item to get
 * @param $args['short'] short string to get
 * @returns array
 * @return abac array, or false on failure
 */
function MultiHook_userapi_get($args)
{
    if (!SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_READ)) {
        return LogUtil::registerError(_MH_NOAUTH);
    }

    pnModDBInfoLoad('MultiHook', 'MultiHook');
    $pntable =& pnDBGetTables();
    $multihookcolumn = $pntable['multihook_column'];

    $permfilter[] = array ('realm'            =>  0,
                           'component_left'   =>  'MultiHook',
                           'component_middle' =>  '',
                           'component_right'  =>  '',
                           'instance_left'    =>  'short',
                           'instance_middle'  =>  '',
                           'instance_right'   =>  'aid',
                           'level'            =>  ACCESS_READ);

    if (isset($args['aid'])) {
        if(is_numeric($args['aid'])) {
            // Get item
            $abac = DBUtil::selectObjectByID('multihook', $args['aid'], 'aid', null, $permfilter);
        } else {
            return LogUtil::registerError(_MODARGSERROR . ' in MultiHook_userapi_get() [aid]');
        }
    } else if(isset($args['short'])) {
        if(!empty($args['short'])) {
            // Get item
            $where = "WHERE $multihookcolumn[short] = '" . DataUtil::formatForStore($args['short']) . "'";
            $abac = DBUtil::selectObject('multihook', $where, null, $permfilter);
        } else {
            return LogUtil::registerError(_MODARGSERROR . ' in MultiHook_userapi_get() [short]');
        }
    } else {
        return LogUtil::registerError (_MODARGSERROR);
    }
    
    if($abac == false) {
        return LogUtil::registerError (_MH_SELECTFAILED);
    }
    return $abac;
}

/**
 * count the number of items in the database
 * @params $args['filter'] int 0=abbr, 1=acronyms, 2=links, 3=censor
 * @returns integer
 * @returns number of items in the database
 */
function MultiHook_userapi_countitems($args)
{
    if (!SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_READ)) {
        return LogUtil::registerError(_MH_NOAUTH);
    }

    pnModDBInfoLoad('MultiHook', 'MultiHook');
    $pntable =& pnDBGetTables();
    $multihookcolumn = $pntable['multihook_column'];

    $where = '';
    if(isset($args['filter']) && is_numeric($args['filter']) && ($args['filter']>=0 && $args['filter']<=3)) {
        $where = "WHERE $multihookcolumn[type]=" . DataUtil::formatForStore($args['filter']);
    }

    $objcount = DBUtil::selectObjectCount ('multihook', $where);
    return $objcount;
}

/**
 * transform text
 * @param $args['extrainfo'] string or array of text items
 * @returns string
 * @return string or array of transformed text items
 */
function MultiHook_userapi_transform($args)
{
    // Argument check
    if (!isset($args['extrainfo'])) {
        return LogUtil::registerError(_MODARGSERROR . ' in MultiHook_userapi_transform() [extrainfo]');
        return;
    }

    if (is_array($args['extrainfo'])) {
        $transformed = array();
        foreach($args['extrainfo'] as $text) {
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

    // pad it with a space so we can distinguish between FALSE and matching the 1st char (index 0).
    $text = ' '  . $text;

    
    // add stylesheet
    pnPageAddVar('stylesheet', 'modules/MultiHook/pnstyle/mh.css');

    static $search = array();
    static $replace = array();
    static $finalsearch = array();
    static $finalreplace = array();
    static $gotabbreviations = 0;
    static $gotneedles = 0;

    static $mhadmin;
    if(!isset($mhadmin)) {
        $mhadmin = SecurityUtil::checkPermission('MultiHook::', '::', ACCESS_DELETE);
    }

    static $mhincodetags;
    if(!isset($mhincodetags)) {
        $mhincodetags = (pnModGetVar('MultiHook', 'mhincodetags')==1) ? true : false;
    }

    static $mhshoweditlink;
    if(!isset($mhshoweditlink)) {
        $mhshoweditlink = (pnModGetVar('MultiHook', 'mhshoweditlink')==1) ? true : false;
    }

    static $onlyonce;
    if(!isset($onlyonce)) {
        $onlyonce = (pnModGetVar('MultiHook', 'abacfirst')==1) ? true : false;
    }

    static $haveoverlib;
    if(!isset($haveoverlib)) {
        $haveoverlib = pnModAvailable('overlib');
    }

    $needles = @unserialize(pnModGetVar('MultiHook', 'needles'));
    if(!is_array($needles)) {
        $needles = array();
    }

    // deal with munded words (leet speak)
    $leetsearch  = array('/o/i', '/e/i', '/a/i', '/i/i');
    $leetreplace = array('0', '3', '@', '1');

    // current url and uri
    $currenturl = pnGetCurrentURL();
    $currenturi = pnGetCurrentURI();

    // Step 0 - move all bbcode with [code][/code] out of the way
    //          if MultiHook is configured accordingly
    if($mhincodetags==false) {
        // if we are faster than pn_bbcode, we will have to remove the code tags
        $codecount1 = preg_match_all("/\[code(.*)\](.*)\[\/code\]/siU", $text, $codes1);
        for($i=0; $i < $codecount1; $i++) {
            $text = str_replace($codes1[0][$i], " MULTIHOOKCODE1REPLACEMENT{$i} ", $text);
            //$text = preg_replace('/(' . preg_quote($codes1[0][$i], '/') . ')/', " MULTIHOOKCODE1REPLACEMENT{$i} ", $text, 1);
        }
        // but pn_bbode may have been faster than we are,. To avoid any problems its embraces the
        // replaced code tags with <!--code--> and <!--/code-->
        // this is what we are taking care of now
        $codecount2 = preg_match_all("/<!--code-->(.*)<!--\/code-->/siU", $text, $codes2);
        for($i=0; $i < $codecount2; $i++) {
            $text = str_replace($codes2[0][$i], " MULTIHOOKCODE2REPLACEMENT{$i} ", $text);
            //$text = preg_replace('/(' . preg_quote($codes2[0][$i], '/') . ')/', " MULTIHOOKCODE2REPLACEMENT{$i} ", $text, 1);
        }
    }

    // Step 1 - move all links out of the text and replace them with placeholders
    $tagcount = preg_match_all('/<a(.*)>(.*)<\/a>/siU', $text, $tags);
    for ($i = 0; $i < $tagcount; $i++) {
        $text = preg_replace('/(' . preg_quote($tags[0][$i], '/') . ')/', " MULTIHOOKTAGREPLACEMENT{$i} ", $text, 1);
    }

    // Step 2 - remove all html tags, we do not want to change them!!
    $htmlcount = preg_match_all("/<(?:[^\"\']+?|.+?(?:\"|\').*?(?:\"|\')?.*?)*?>/si", $text, $html);
    for ($i=0; $i < $htmlcount; $i++) {
        $text = preg_replace('/(' . preg_quote($html[0][$i], '/') . ')/', " MULTIHOOKHTMLREPLACEMENT{$i} ", $text, 1);
    }

    // Step 3 - move all bbcode with [url][/url] out of the way
    $urlcount = preg_match_all("#\[url(.*)\](.*)\[\/url\]#siU", $text, $urls);
    for($i=0; $i < $urlcount; $i++) {
        $text = preg_replace('/(' . preg_quote($urls[0][$i], '/') . ')/', " MULTIHOOKURLREPLACEMENT{$i} ", $text, 1);
    }

    // Step 4 - move all urls starting with http:// etc. out of the way
    $linkcount = preg_match_all("/(http|https|ftp|ftps|news)\:\/\/([a-zA-Z0-9\-\._]+[\.]{1}[a-zA-Z]{2,6})(\/[a-zA-Z0-9\-\.\?\,\'\/\\\+&%\$#_=~]+)?/siU", $text, $links);
    for($i=0; $i < $linkcount; $i++) {
        $text = preg_replace('/(' . preg_quote($links[0][$i], '/') . ')/', " MULTIHOOKLINKREPLACEMENT{$i} ", $text, 1);
    }

    // Step 5 - move hilite hook additions out of the text
    $hilitecount = preg_match_all("/<!--hilite-->(.*)<!--\/hilite-->/siU", $text, $hilite);
    for($i=0; $i < $hilitecount; $i++) {
        $text = preg_replace('/(' . preg_quote($hilite[0][$i], '/') . ')/', " MULTIHOOKHILITEREPLACEMENT{$i} ", $text, 1);
    }

    if (empty($gotabbreviations)) {
        $gotabbreviations = 1;
        $tmps = pnModAPIFunc('MultiHook', 'user', 'getall', array('sortbylength' => true));
        // Create search/replace array from abbreviations/links information

        foreach ($tmps as $tmp) {
            // check if the current tmp is a link
            //save original long
            $tmp['long_original'] = $tmp['long'];
            if($tmp['type']==2) {
                $tmp['long'] = absolute_url($tmp['long']);
            }

            $tmp['long']  = preg_replace('/(\b)/', '\\1MULTIHOOKTEMPORARY', $tmp['long']);
            $tmp['title'] = preg_replace('/(\b)/', '\\1MULTIHOOKTEMPORARY', $tmp['title']);

            if($tmp['type']==0) {
                // 0 = Abbreviation
                $search_temp = '/(?<![\/\w@\.:])(' . preg_quote($tmp['short'], '/'). ')(?![\/\w@:])(?!\.\w)/i';
                $search[]      = $search_temp;
                $replace[]     = md5($search_temp);
                $finalsearch[] = '/' . preg_quote(md5($search_temp), '/') . '/';
                $finalreplace[] = create_abbr($tmp, $mhadmin, $mhshoweditlink, $haveoverlib);
                unset($search_temp);
            } else if($tmp['type']==1) {
                // 1 = Acronym
                $search_temp = '/(?<![\/\w@\.:])(' . preg_quote($tmp['short'], '/'). ')(?![\/\w@:])(?!\.\w)/i';
                $search[]      = $search_temp;
                $replace[]     = md5($search_temp);
                $finalsearch[] = '/' . preg_quote(md5($search_temp), '/') . '/';
                $finalreplace[] = create_acronym($tmp, $mhadmin, $mhshoweditlink, $haveoverlib);
                unset($search_temp);
            } else if($tmp['type']==2) {
                // 2 = Link
                // don't show link if the target is the current url
                if($tmp['long_original'] <> $currenturl && $tmp['long_original'] <> $currenturi) {
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
                    $finalreplace[] = create_link($tmp, $mhadmin, $mhshoweditlink, $haveoverlib);
                    unset($search_temp);
                }
            } else if($tmp['type']==3) {
                // original censored word
                $search_temp = '/(?<![\/\w@\.:])(' . preg_quote($tmp['short'], '/'). ')(?![\/\w@:])(?!\.\w)/i';
                $search[]      = $search_temp;
                $replace[]     = md5($search_temp);
                $finalsearch[] = '/' . preg_quote(md5($search_temp), '/') . '/';
                $finalreplace[] = create_censor($tmp, $mhadmin, $mhshoweditlink, $haveoverlib);
                
                // Common replacements
                $mungedword = preg_replace($leetsearch, $leetreplace, $tmp['short']);
                if ($mungedword != $tmp['short']) {
                    $search_temp = '/(?<![\/\w@\.:])(' . preg_quote($mungedword, '/'). ')(?![\/\w@:])(?!\.\w)/i';
                    $search[]      = $search_temp;
                    $replace[]     = md5($search_temp);
                    $finalsearch[] = '/' . preg_quote(md5($search_temp), '/') . '/';
                    $finalreplace[] = create_censor($tmp, $mhadmin, $mhshoweditlink, $haveoverlib);
                }
                unset($search_temp);
            }
        } // foreach
    }
    // check for needles
    if(count($needles) > 0) {
        foreach($needles as $needle) {
            preg_match_all('/(?<![\/\w@\.:])' . strtoupper($needle['needle']) . '([a-zA-Z0-9_-]*?)(?![\/\w@:-])(?!\.\w)/', $text, $needleresults);
            if(is_array($needleresults) && count($needleresults[0])>0) {
                // complete needle in $needleresults[0], needle id in $needleresults[1]
                // both are arrays!
                for($ncnt = 0; $ncnt<count($needleresults[0]); $ncnt++) {
                    $search_temp = '/(?<![\/\w@\.:])(' . preg_quote($needleresults[0][$ncnt], '/'). ')(?![\/\w@:-])(?!\.\w)/';
                    $search[]      = $search_temp;
                    $replace[]     = md5($search_temp);
                    $finalsearch[] = '/' . preg_quote(md5($search_temp), '/') . '/';
                    $finalreplace[] = pnModAPIFunc('MultiHook', 'needle', strtolower($needle['needle']),
                                               array('nid' => $needleresults[1][$ncnt]));
                    unset($search_temp);
                }
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
            $text = str_replace(" MULTIHOOKCODE2REPLACEMENT{$i} ", $codes2[0][$i], $text);
            //$text = preg_replace("/ MULTIHOOKCODE2REPLACEMENT{$i} /", $codes2[0][$i], $text, 1);
        }
        for ($i = 0; $i < $codecount1; $i++) {
            $text = str_replace(" MULTIHOOKCODE1REPLACEMENT{$i} ", $codes1[0][$i], $text);
            //$text = preg_replace("/ MULTIHOOKCODE1REPLACEMENT{$i} /", $codes1[0][$i], $text, 1);
        }
    }

    // Remove our padding from the string..
    $text = substr($text, 1);
    return $text;
}

?>
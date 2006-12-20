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
// Purpose of file:  common functions
// ----------------------------------------------------------------------

function create_abbr($abac, $mhadmin=false, $mhshoweditlink=false, $haveoverlib=false)
{
    extract($abac);

    static $mhreplaceabbr;
    if(!isset($mhreplaceabbr)) {
        $mhreplaceabbr = (pnModGetVar('MultiHook', 'mhreplaceabbr')==1) ? true : false;
    }

    $xhtmllang = get_xhtml_language($language);

    $long  = DataUtil::formatForDisplayHTML($long);
    $short = DataUtil::formatForDisplayHTML($short);

    $replace_temp = '';
    if($mhreplaceabbr==false) {
        if($haveoverlib) {
            $replace_temp = '<abbr '.$xhtmllang.' onmouseover="return overlib(\'' . $long . '\', CAPTION, \'' . DataUtil::formatForDisplay(_MH_ABBREVIATION) . ': '. $short .'\', ' . overlib_params() . ')" onmouseout="return nd();"><span class="abbr" onmouseover="return overlib(\'' . $long . '\', CAPTION, \'' . DataUtil::formatForDisplay(_MH_ABBREVIATION) . ': '. $short .'\')" onmouseout="return nd();">' . $short . '</span></abbr>';
        } else {
            $replace_temp = '<abbr '.$xhtmllang.' title="' . $long . '"><span class="abbr" title="'. $long .'">' . $short . '</span></abbr>';
        }

    } else {
        $replace_temp = $long;
    }

    if($mhadmin==true && $mhshoweditlink==true) {
        $replace_temp = '<span>' . $replace_temp . '<img src="modules/MultiHook/pnimages/edit.gif" width="7" height="7" alt="" class="multihookeditlink" title="' . DataUtil::formatForDisplay(_EDIT) . ': ' . $short . ' (' . DataUtil::formatForDisplay(_MH_ABBREVIATION) . ') #' . $aid . '" />' . '</span>';
    }

    return $replace_temp;

}

function create_acronym($abac, $mhadmin=false, $mhshoweditlink=false, $haveoverlib=false)
{
    extract($abac);
    $long  = DataUtil::formatForDisplayHTML($long);
    $short = DataUtil::formatForDisplayHTML($short);

    $xhtmllang = get_xhtml_language($language);

    if($haveoverlib) {
        $replace_temp = '<acronym '.$xhtmllang.' onmouseover="return overlib(\'' . $long . '\', CAPTION, \'' . DataUtil::formatForDisplay(_MH_ACRONYM) . ': '. $short .'\', ' . overlib_params() . ')" onmouseout="return nd();">' . $short . '</acronym>';
    } else {
        $replace_temp = '<acronym '.$xhtmllang.' title="' . $long . '">' . $short . '</acronym>';
    }

    if($mhadmin==true && $mhshoweditlink==true) {
        $replace_temp = '<span>' . $replace_temp . '<img src="modules/MultiHook/pnimages/edit.gif" width="7" height="7" alt="" class="multihookeditlink" title="' . DataUtil::formatForDisplay(_EDIT) . ': ' . $short . ' (' . DataUtil::formatForDisplay(_MH_ACRONYM) . ') #' . $aid . '" />' . '</span>';
    }

    return $replace_temp;
}

function create_link($abac, $mhadmin=false, $mhshoweditlink=false, $haveoverlib=false)
{
    extract($abac);

    static $mhlinktitle;
    static $externallinkclass;

    if(!isset($mhlinktitle)) {
        $mhlinktitle = (pnModGetVar('MultiHook', 'mhlinktitle')==1) ? true : false;
    }
    if(!isset($externallinkclass)) {
        $externallinkclass =pnModGetVar('MultiHook', 'externallinkclass');
    }

    if(preg_match("/(^http:\/\/)/", $long_original)==1) {
        if(!empty($externallinkclass)) {
            $extclass = "class=\"$externallinkclass\"";
        }
        $accessebilityhack = ''; // <span class="mhacconly"> ' . DataUtil::formatForDisplay(_MH_EXTERNALLINK) . '</span>';
    } else {
        $extclass = '';
        $accessebilityhack = '';
    }

    // prepare url
    $long = DataUtil::formatForDisplay($long);
    $aid  = DataUtil::formatForDisplay($aid);
    $short = DataUtil::formatForDisplayHTML($short);
    $title = DataUtil::formatForDisplayHTML($title);

    if($mhlinktitle==false) {
        if($haveoverlib) {
            $replace_temp = '<a '.$extclass.' href="' . $long . '" title="" onmouseover="return overlib(\'' . $long . '\', CAPTION, \''. $title .'\', ' . overlib_params() . ')" onmouseout="return nd();">' . $short . $accessebilityhack . '</a>';
        } else {
            $replace_temp = '<a '.$extclass.' href="' . $long . '" title="' . $title . '">' . $short . $accessebilityhack . '</a>';
        }
    } else {
        if($haveoverlib) {
            $replace_temp = '<a '.$extclass.' href="' . $long . '" title="" onmouseover="return overlib(\'' . $long . '\', CAPTION, \''. $title .'\', ' . overlib_params() . ')" onmouseout="return nd();">' . $title . $accessebilityhack . '</a>';
        } else {
            $replace_temp = '<a '.$extclass.' href="' . $long . '" title="' . $title . '">' . $title . $accessebilityhack . '</a>';
        }
    }
    if($mhadmin==true && $mhshoweditlink==true) {
        $replace_temp = '<span>' . $replace_temp . '<img src="modules/MultiHook/pnimages/edit.gif" width="7" height="7" alt="" class="multihookeditlink" title="' . DataUtil::formatForDisplay(_EDIT) . ': ' . $short . ' (' . DataUtil::formatForDisplay(_MH_LINK) . ') #' . $aid . '" />' . '</span>';
    }
    return $replace_temp;
}

function create_censor($abac, $mhadmin=false, $mhshoweditlink=false, $haveoverlib=false)
{
    extract($abac);

    $replace_temp = str_repeat('*', strlen($abac['short']));

    if($mhadmin==true && $mhshoweditlink==true) {
        $replace_temp = '<span>' . $replace_temp . '<img src="modules/MultiHook/pnimages/edit.gif" width="7" height="7" alt="" class="multihookeditlink" title="' . DataUtil::formatForDisplay(_EDIT) . ': ' . $short . ' (' . DataUtil::formatForDisplay(_MH_CENSOR) . ') #' . $aid . '" />' . '</span>';
    }

    return $replace_temp;

}

function overlib_params()
{
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
    return $overlib_parameters;
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

function absolute_url($url='')
{
    static $schemes = array('http', 'https', 'ftp', 'gopher', 'ed2k', 'news', 'mailto', 'telnet');

    if(strlen($url) > 0) {
        // make sure that relative urls get converted to absolute urls (safehtml needs this)
        $exploded_url = explode(':', $url);
        if(!in_array($exploded_url[0], $schemes)) {
            // url does not start with one of the schemes defined above - we consider it
            // being a relative path now
            // next check for leading / in  relative url
            if($url[0] == '/') {
                // and remove it
                $url = substr($url, 1);
            }
            $url = pnGetBaseURL() . $url;
        }
    }
    return $url;
}

function mh_ajaxerror($error='')
{
    if(!empty($error)) {
        header('HTTP/1.0 400 Bad Data');
        echo $error;
        exit;
    }
}

  
?>
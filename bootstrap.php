<?php
/**
 * Multihook
 *
 * @copyright (c) 2001-now, Multihook Development Team
 * @link http://code.zikula.org/multihook
 * @version $Id: bootstrap.php -1   $
 * @license GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * @package Multihook
 */

EventUtil::attach('theme.postfooter', array('MultiHook_Footer', 'includeFooter'));

$MultiHookAdminApi = 'MultiHook_Api_Admin';
$MultiHookUserApi = 'MultiHook_Api_User';

function mhdebug($name='', $data, $die = false)
{
    if (SecurityUtil::checkPermission('Dizkus::', '::', ACCESS_ADMIN)) {
        $type = gettype($data);
        echo "\n<!-- begin debug of $name -->\n<div style=\"color: red;\">$name ($type";
        if (is_array($data)||is_object($data)) {
            if (count($data) > 0) {
                echo ', size='.count($data).'):<pre>';
                echo htmlspecialchars(print_r($data, true));
                echo '</pre>:<br />';
            } else {
                echo '):empty<br />';
            }
        } else if (is_bool($data)) {
            echo ($data==true) ? ") true<br />" : ") false<br />";
        } else if (is_string($data)) {
            echo ', len='.strlen($data).') :'.DataUtil::formatForDisplay($data).':<br />';
        } else {
            echo ') :'.$data.':<br />';
        }
        echo "</div><br />\n<!-- end debug of $name -->";
        if ($die==true) {
            System::shutDown();
        }
    }
}

function create_abbr($abac, $mhadmin=false, $mhshoweditlink=false, $haveoverlib=false)
{
    static $mhreplaceabbr;
    if(!isset($mhreplaceabbr)) {
        $mhreplaceabbr = (ModUtil::getVar('MultiHook', 'mhreplaceabbr')==1) ? true : false;
    }

    $xhtmllang = get_xhtml_language($abac['language']);

    $long  = DataUtil::formatForDisplayHTML($abac['long']);
    $short = DataUtil::formatForDisplayHTML($abac['short']);
    $aid   = DataUtil::formatForDisplayHTML($abac['aid']);

    $replace_temp = '';
    if($mhreplaceabbr==false) {
        if($haveoverlib) {
            $replace_temp = '<abbr '.$xhtmllang.' onmouseover="return overlib(\'' . $long . '\', CAPTION, \'' . DataUtil::formatForDisplay(__('Abbreviations')) . ': '. $short .'\', ' . overlib_params() . ')" onmouseout="return nd();"><span class="abbr" onmouseover="return overlib(\'' . $long . '\', CAPTION, \'' . DataUtil::formatForDisplay(__('Abbreviations')) . ': '. $short .'\')" onmouseout="return nd();">' . $short . '</span></abbr>';
        } else {
            $replace_temp = '<abbr '.$xhtmllang.' title="' . $long . '"><span class="abbr" title="'. $long .'">' . $short . '</span></abbr>';
        }

    } else {
        $replace_temp = $long;
    }

    if($mhadmin==true && $mhshoweditlink==true) {
        $replace_temp = '<span>' . $replace_temp . '<img src="modules/MultiHook/images/edit.gif" width="7" height="7" alt="" class="multihookeditlink" title="' . DataUtil::formatForDisplay(__('Edit')) . ': ' . $short . ' (' . DataUtil::formatForDisplay(__('Abbreviations')) . ') #' . $aid . '" />' . '</span>';
    }

    return $replace_temp;

}

function create_acronym($abac, $mhadmin=false, $mhshoweditlink=false, $haveoverlib=false)
{
    $long  = DataUtil::formatForDisplayHTML($abac['long']);
    $short = DataUtil::formatForDisplayHTML($abac['short']);
    $aid   = DataUtil::formatForDisplayHTML($abac['aid']);

    $xhtmllang = get_xhtml_language($abac['language']);

    if($haveoverlib) {
        $replace_temp = '<acronym '.$xhtmllang.' onmouseover="return overlib(\'' . $long . '\', CAPTION, \'' . DataUtil::formatForDisplay(__('Acronyms')) . ': '. $short .'\', ' . overlib_params() . ')" onmouseout="return nd();">' . $short . '</acronym>';
    } else {
        $replace_temp = '<acronym '.$xhtmllang.' title="' . $long . '">' . $short . '</acronym>';
    }

    if($mhadmin==true && $mhshoweditlink==true) {
        $replace_temp = '<span>' . $replace_temp . '<img src="modules/MultiHook/images/edit.gif" width="7" height="7" alt="" class="multihookeditlink" title="' . DataUtil::formatForDisplay(__('Edit')) . ': ' . $short . ' (' . DataUtil::formatForDisplay(__('Acronyms')) . ') #' . $aid . '" />' . '</span>';
    }

    return $replace_temp;
}

function create_link($abac, $mhadmin=false, $mhshoweditlink=false, $haveoverlib=false)
{
    static $mhlinktitle;
    static $externallinkclass;

    if(!isset($mhlinktitle)) {
        $mhlinktitle = (ModUtil::getVar('MultiHook', 'mhlinktitle')==1) ? true : false;
    }
    if(!isset($externallinkclass)) {
        $externallinkclass =ModUtil::getVar('MultiHook', 'externallinkclass');
    }

    $extclass = '';
    $accessebilityhack = '';
    if(preg_match("/(^http:\/\/)/", $abac['long'])==1) {
        if(!empty($externallinkclass)) {
            $extclass = "class=\"$externallinkclass\"";
        }
        $accessebilityhack = ''; // not working yet: <span class="mhacconly"> ' . DataUtil::formatForDisplay(__('(external link)', $dom)) . '</span>';
    }

    // prepare url
    $long = DataUtil::formatForDisplay($abac['long']);
    $aid  = DataUtil::formatForDisplay($abac['aid']);
    $short = DataUtil::formatForDisplayHTML($abac['short']);
    $title = DataUtil::formatForDisplayHTML($abac['title']);

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
        $replace_temp = '<span>' . $replace_temp . '<img src="modules/MultiHook/images/edit.gif" width="7" height="7" alt="" class="multihookeditlink" title="' . DataUtil::formatForDisplay(__('Edit')) . ': ' . $short . ' (' . DataUtil::formatForDisplay(__('Links')) . ') #' . $aid . '" />' . '</span>';
    }
    return $replace_temp;
}

function create_censor($abac, $mhadmin=false, $mhshoweditlink=false, $haveoverlib=false, $relaxedcensoring=false)
{
    $len = strlen($abac['short']);
    $replace_temp = str_repeat('*', $len);
    if ($relaxedcensoring == true && $len > 2) {
        $replace_temp[0]= $abac['short'][0];
        $id = strlen($replace_temp)-1;
        $replace_temp[$id]= $abac['short'][$len-1];
    }

    $short = DataUtil::formatForDisplay($abac['short']);
    $aid   = DataUtil::formatForDisplay($abac['aid']);
    
    if($mhadmin==true && $mhshoweditlink==true) {
        $replace_temp = '<span>' . $replace_temp . '<img src="modules/MultiHook/images/edit.gif" width="7" height="7" alt="" class="multihookeditlink" title="' . DataUtil::formatForDisplay(__('Edit')) . ': ' . $short . ' (' . DataUtil::formatForDisplay(__('Censor')) . ') #' . $aid . '" />' . '</span>';
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
/*
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
    $lang = (empty($lang)) ? ZLanguage::getLanguageCode() : $lang;
    $shortlang = $alllanguages[$lang];
*/
    $shortlang = ZLanguage::getLanguageCode();
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
            $url = System::getBaseUrl() . $url;
        }
    }
    return $url;
}

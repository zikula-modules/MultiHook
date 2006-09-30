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
// Purpose of file:  MultiHook needle API
// ----------------------------------------------------------------------

/**
 * photoshare needle
 * @param $args['nid'] needle id
 * @return array()
 */
function MultiHook_needleapi_photoshare($args)
{
    // Get arguments from argument array
    $nid = $args['nid'];
    unset($args);
    
    // cache the results
    static $cache;
    if(!isset($cache)) {
        $cache = array();
    } 

    if(!empty($nid)) {
        if(!isset($cache[$nid])) {
            // not in cache array
            if(pnModAvailable('photoshare')) {
                pnModLoad('photoshare', 'user');
                // load language defines from pnlang/xxx/photoshare.php
                pnModLangLoad('MultiHook', 'photoshare');
                
                // nid is like type_albumid, type_imageid or type_imageid_width_height
                $temp = explode('-', $nid);
                $type = '';
                if(is_array($temp) && count($temp)>=2) {
                    $type   = $temp[0];
                    $id     = $temp[1];
                    if($type=='P') {
                        $width  = (isset($temp[2])) ? $temp[2] : null; // if type==P
                        $height = (isset($temp[3])) ? $temp[3] : null; // if type==P
                    }
                }
                
                switch($type) {
                    case 'A':
                        // show link to folder,display folder name
                        // not in cache array
                        $folder =  pnModAPIFunc('photoshare',
                                                'user',
                                                'get_folder_info',
                                                array('folderID' => $id) );
                        if(is_array($folder)) {
                            if(photoshareAccessFolder($id, photoshareAccessRequirementView, '')) {
                                $url   = pnVarPrepForDisplay(pnModURL('photoshare', 'user', 'showimages', array('fid' => $id)));
                                $title = pnVarPrepForDisplay($folder['title']);
                                $cache[$nid] = '<a href="' . $url . '" title="' . $title . '">' . $title . '</a>';
                            } else {
                                $cache[$nid] = '<em>' . pnVarPrepForDisplay(_MH_PS_NOAUTHFORFOLDER . ' (' . $id . ')') . '</em>';
                            }
                        } else {
                            $cache[$nid] = '<em>' . pnVarPrepForDisplay(_MH_PS_UNKNOWNFOLDER .  ' (' . $id . ')') . '</em>';
                        }
                        break;
                    case 'P':
                        // show image
                    case 'T':
                        // show thumbnail with link to fullsize image
                        // not in cache array
                        $image = pnModAPIFunc('photoshare', 'show', 'get_image_info', 
                                              array('imageID' => $id));
                        if(is_array($image) && isset($image['id'])) {
                            if(photoshareAccessImage($id, photoshareAccessRequirementView, '')) {
                                if($type=='P') {
                                    // Picture
                                    $url   = pnVarPrepForDisplay(pnModURL('photoshare', 'user', 'viewimage', array('iid' => $id)));
                                    $title = pnVarPrepForDisplay($image['title']);
                                    $widthheight = '';
                                    if(isset($width) && isset($height)) {
                                        $widthheight = ' width="' . $width . '" height="' . $height . '"';
                                    }
                                    $cache[$nid] = '<img src="' . $url . '" title="' . $title . '" alt="' . $title . '"' . $widthheight . ' />';
                                } else {
                                    // Thumbnail
                                    $fullurl   = pnVarPrepForDisplay(pnModURL('photoshare', 'user', 'viewimage', array('iid' => $id)));
                                    $thumburl = $fullurl . '&amp;thumbnail=1';
                                    $title = pnVarPrepForDisplay($image['title']);
                                    $cache[$nid] = '<a href="' . $fullurl . '" title="' . $title . '"><img src="' . $thumburl . '" alt="' . $title . '" /></a>';
                                }
                            } else {
                                $cache[$nid] = '<em>' . pnVarPrepForDisplay(_MH_PS_NOAUTHFORIMAGE . ' (' . $id . ')') . '</em>';
                            }
                        } else {
                            $cache[$nid] = '<em>' . pnVarPrepForDisplay(_MH_PS_UNKNOWNIMAGE .  ' (' . $id . ')') . '</em>';
                        }
                        break;
                    default:
                        $cache[$nid] = '<em>' . pnVarPrepForDisplay(_MH_PS_UNKNOWNTYPE) . '</em>';
                        
                }
            } else {
                $cache[$nid] = '<em>' . pnVarPrepForDisplay(_MH_PS_NOTAVAILABLE) . '</em>';
            }
        }
        $result = $cache[$nid];
    } else {
        $result = '<em>' . pnVarPrepForDisplay(_MH_PS_NONEEDLEID) . '</em>';
    }
    return $result;
    
}

?>
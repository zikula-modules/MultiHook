<?php
// $Id: pnadminapi.php 73 2006-07-16 09:21:42Z landseer $
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

    $result = '<em title="' . pnVarPrepForDisplay(sprintf(_MH_NEEDLEDATAERROR, $nid, 'photoshare')) . '">PHOTOSHARE' . $nid . '</em>';
    if(!isset($cache[$nid])) {
        // not in cache array
        // set the default
        $cache[$nid] = $result;
        if(pnModAvailable('photoshare')) {
            
            // nid is like type_albumid, type_imageid or type_imageid_width_height
            $temp = explode('_', $nid);
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
                    // not in cache array
                    $folder =  pnModAPIFunc('photoshare',
                                            'user',
                                            'get_folder_info',
                                            array('folderID' => $id) );
        
                    $url   = pnVarPrepForDisplay(pnModURL('photoshare', 'user', 'showimages', array('fid' => $id)));
                    $title = pnVarPrepForDisplay($folder['title']);
                    $cache[$nid] = '<a href="' . $url . '" title="' . $title . '">' . $title . '</a>';
                    break;
                case 'P':
                    // not in cache array
                    $image = pnModAPIFunc('photoshare', 'show', 'get_image_info', 
                                          array('imageID' => $id));
                    $url   = pnVarPrepForDisplay(pnModURL('photoshare', 'user', 'viewimage', array('iid' => $id)));
                    $title = pnVarPrepForDisplay($image['title']);
                    $widthheight = '';
                    if(isset($width) && isset($height)) {
                        $widthheight = ' width="' . $width . '" height="' . $height . '"';
                    }
                    $cache[$nid] = '<img src="' . $url . '" title="' . $title . '" alt="' . $title . '"' . $widthheight . ' />';
                    break;
                case 'T':
                    // not in cache array
                    $image = pnModAPIFunc('photoshare', 'show', 'get_image_info', 
                                          array('imageID' => $id));
                    $fullurl   = pnVarPrepForDisplay(pnModURL('photoshare', 'user', 'viewimage', array('iid' => $id)));
                    $thumburl = $fullurl . '&amp;thumbnail=1';
                    $title = pnVarPrepForDisplay($image['title']);
                    $cache[$nid] = '<a href="' . $fullurl . '" title="' . $title . '"><img src="' . $thumburl . '" alt="' . $title . '" /></a>';
                    break;
                default:
                    // default already set before
            }
        }
        $result = $cache[$nid];
    }
    return $result;
    
}

?>
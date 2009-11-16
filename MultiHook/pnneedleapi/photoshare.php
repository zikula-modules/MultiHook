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

/**
 * photoshare needle
 * @param $args['nid'] needle id
 * @return array()
 */
function MultiHook_needleapi_photoshare($args)
{
    $dom = ZLanguage::getModuleDomain('MultiHook');
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

                // nid is like type_albumid, type_imageid or type_imageid_width_height
                $temp = explode('-', $nid);
                $type = '';
                if(is_array($temp)) {
                    $type   = $temp[0];
                    $id  = (isset($temp[1])) ? $temp[1] : null;
                    if($type=='P') {
                        $width  = (isset($temp[2])) ? $temp[2] : null; // if type==P
                        $height = (isset($temp[3])) ? $temp[3] : null; // if type==P
                        $align  = (isset($temp[4])) ? strtolower($temp[4]) : null; // if type==P
                        if(isset($align) && $align <> 'left' && $align <> 'right') {
                            unset($align);
                        }
                    }
                    // only type==C allows an empty id:
                    if($type<>'C' && !isset($id)) {
                        $type = '';
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
                                $url   = DataUtil::formatForDisplay(pnModURL('photoshare', 'user', 'showimages', array('fid' => $id)));
                                $title = DataUtil::formatForDisplay($folder['title']);
                                $cache[$nid] = '<a href="' . $url . '" title="' . $title . '">' . $title . '</a>';
                            } else {
                                $cache[$nid] = '<em>' . DataUtil::formatForDisplay(__('No auth for folder', $dom) . ' (' . $id . ')') . '</em>';
                            }
                        } else {
                            $cache[$nid] = '<em>' . DataUtil::formatForDisplay(__('Unkown folder', $dom) .  ' (' . $id . ')') . '</em>';
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
                                    $url   = DataUtil::formatForDisplay(pnModURL('photoshare', 'user', 'viewimage', array('iid' => $id)));
                                    $title = DataUtil::formatForDisplay($image['title']);
                                    $widthheight = '';
                                    if(isset($width) && isset($height)) {
                                        $widthheight = ' width="' . $width . '" height="' . $height . '"';
                                    }
                                    $cache[$nid] = '<img src="' . $url . '" title="' . $title . '" alt="' . $title . '"' . $widthheight . ' />';
                                    if(isset($align)) {
                                        // validity checks have been done before
                                        $cache[$nid] = '<span style="float: ' . $align . '">'. $cache[$nid] . '</span>';
                                    }
                                } else {
                                    // Thumbnail
                                    $fullurl   = DataUtil::formatForDisplay(pnModURL('photoshare', 'user', 'viewimage', array('iid' => $id)));
                                    $thumburl = $fullurl . '&amp;thumbnail=1';
                                    $title = DataUtil::formatForDisplay($image['title']);
                                    $cache[$nid] = '<a href="' . $fullurl . '" title="' . $title . '"><img src="' . $thumburl . '" alt="' . $title . '" /></a>';
                                }
                            } else {
                                $cache[$nid] = '<em>' . DataUtil::formatForDisplay(__('No auth for photoshare image', $dom) . ' (' . $id . ')') . '</em>';
                            }
                        } else {
                            $cache[$nid] = '<em>' . DataUtil::formatForDisplay(__('Unkown photoshare image', $dom) .  ' (' . $id . ')') . '</em>';
                        }
                        break;
                    case 'C':
                        $cache[$nid] = '<span style="clear: both;">&nbsp;</span>';
                        break;
                    default:
                        $cache[$nid] = '<em>' . DataUtil::formatForDisplay(__('unkown parameter at pos 1 (A,P or T)', $dom)) . '</em>';

                }
            } else {
                $cache[$nid] = '<em>' . DataUtil::formatForDisplay(__('Pagesetter not available', $dom)) . '</em>';
            }
        }
        $result = $cache[$nid];
    } else {
        $result = '<em>' . DataUtil::formatForDisplay(__('no needle id', $dom)) . '</em>';
    }
    return $result;

}

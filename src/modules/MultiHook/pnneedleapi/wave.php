<?php
// $Id: wave.php 224 2010-05-24 20:33:44Z drak $
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

/**
 * wave needle
 * @param $args['nid'] needle id
 * @return array()
 */
function MultiHook_needleapi_wave($args)
{
    static $wid;

    if (!isset($wid)) {
        $wid = 1;
    }

    // simple replacement, no need to cache anything
    if (isset($args['nid']) && !empty($args['nid'])) {
        if (substr($args['nid'], 0, 1) == '-') {
            $args['nid'] = substr($args['nid'], 1);
        }
        $waveid = $args['nid'];

        $result = '<div id="wave'.$wid.'" style="width: 560px; height: 420px"></div>';
        $script = "<script type=\"text/javascript\">
                   Event.observe(window, 'load', function() {
                       var wave$wid = new WavePanel('https://wave.google.com/wave/');
                       wave$wid.setUIConfig('white', 'black', 'Arial', '13px');
                       wave$wid.loadWave('googlewave.com!w+$waveid');
                       wave$wid.init(document.getElementById('wave$wid'));
                   }, false);
                   </script>";

        PageUtil::addVar('javascript', 'http://wave-api.appspot.com/public/embed.js');
        PageUtil::addVar('javascript', 'javascript/ajax/prototype.js');
        PageUtil::addVar('rawtext', $script);
    } else {
        $result = 'No Wave ID';
    }
    return $result;
}

<?php
// $Id$
// =======================================================================
// pixelnet (c) Frank Schummertz 2004
// ----------------------------------------------------------------------
// For POST-NUKE Content Management System
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
// but WIthOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// To read the license please visit http://www.gnu.org/copyleft/gpl.html
// =======================================================================


function pixelnet_user_startsession()
{
    pnModAPILoad('pixelnet', 'user');
    return pnModAPIFunc('pixelnet', 'user', 'startsession');
}

function pixelnet_user_sessionstarted()
{
    list($sid,
         $bid,
         $imgno,
         $errno )  = pnVarCleanFromInput('sid', 'bid', 'imgno', 'errno');

    pnSessionSetVar('pixelnet_imgno', $imgno);
    pnSessionSetVar('pixelnet_sid', $sid);
    pnSessionSetVar('pixelnet_bid', $bid);
    $fid = pnSessionGetVar('pixelnet_fid');
    
    $redirect = pnModURL('photoshare', 'user', 'showimages', array('fid' => $fid));

    pnRedirect($redirect);
    return true;
}

function pixelnet_user_gotoshop()
{
    $shopURL = "http://www.pixelnet.de/upload/";
    $vcode = pnModGetVar('pixelnet', 'vcode');

    $sid = pnSessionGetVar('pixelnet_sid');
    $bid = pnSessionGetVar('pixelnet_bid');
    
    $redirect = $shopURL . "?datei=list.php3&sid=$sid&bid=$bid";
    pnRedirect($redirect);
    return true;
}

function pixelnet_user_addimage()
{
    $pid = pnVarCleanFromInput('pid');

    $shopURL = "http://www.pixelnet.de/upload/";
    $vcode = pnModGetVar('pixelnet', 'vcode');

    $sid = pnSessionGetVar('pixelnet_sid');
    $bid = pnSessionGetVar('pixelnet_bid');
    
    $imgno = pnSessionGetVar('pixelnet_imgno');
    $imgno++;
    pnSessionSetVar('pixelnet_imgno', $imgno);
    
    $imageurl = urlencode(pnGetBaseURL() . strstr(pnModGetVar('photoshare', 'imagedirname'), 'modules') . '/img' . $pid);
    // check if an hires pic exists. if yes, we will send this for printing
    $hires = pnModGetVar('photoshare', 'imagedirname') . '/img' . $pid . '_hires.jpg';
    if(file_exists($hires)) {
        $imageurl = urlencode(pnGetBaseURL() . strstr($hires, 'modules'));
    }
    $redirect = urlencode(pnModURL('pixelnet', 'user', 'imageadded', array('imgno' => $imgno))); 
	$addimage = $shopURL . "extern.php3?anz=1&sid=$sid&bid=$bid&bild1=$imageurl&redirecturl=$redirect";
    pnRedirect($addimage);
    return true;
}

function pixelnet_user_imageadded()
{
    $fid = pnSessionGetVar('pixelnet_fid');
    
    $redirect = pnModURL('photoshare', 'user','showimages', array('fid' => $fid));

    pnRedirect($redirect);
    return true;
}
?>
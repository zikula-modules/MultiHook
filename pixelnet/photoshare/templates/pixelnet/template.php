<?php
// =======================================================================
// Photoshare by Jorn Lind-Nielsen (C) 2002.
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

class PixelnetTemplate
{
  function getVersion()
  {
    return 1;
  }


  function getTitle()
  {
    return _PTMP_PIXELNET_TITLE;
  }


  function getFolderDepth()
  {
    return 1;
  }


  function usesStyleSheet()
  {
    return false;
  }


  function show(&$output, $templateData, $templateHelper)
  {
    $images       = $templateData['images'];
    $folderID     = $templateData['folder']['id'];
    $title        = $templateData['folder']['title'];
    $description  = $templateData['folder']['description'];
    $columnNumber = 5;

    // store the folderID
    pnSessionSetVar('pixelnet_fid', $folderID);

    $pixelnet_sid = pnSessionGetVar('pixelnet_sid');
    if(strlen($pixelnet_sid) == 0) {
        // no valid sid, start a new session
        pnModAPILoad('pixelnet', 'user');
        pnModAPIFunc('pixelnet', 'user', 'startsession');
    }
    
    $this->showTitle($output, $title, $description, $folderID);

      // Check for empty album
    if (count($images) > 0)
    {
        // Iterate through the images and keep count on the position in order to known when to break the line.

      $pos = 0;
      $lastImage = count($images)-1;

      $thumbnailSize = pnModGetVar('photoshare', 'thumbnailsize');
      $cellWidth = $thumbnailSize + $thumbnailSize/5;

      $output->SetInputMode(_PNH_VERBATIMINPUT);
      $output->Text('<table>');

      foreach ($images as $image)
      {
        if ($pos % $columnNumber == 0)
          $output->Text('<tr>');

        $output->Text("<td style=\"text-align:center; border: 1px solid black; width:$cellWidth; height:$cellWidth\" valign=\"top\">");
        $output->Text( $this->getThumbnailHTML($image['title'], $image['id'], $pos, $folderID, $templateHelper) );
        $output->Text("</td>");

        if ($pos % $columnNumber == $columnNumber-1)
        {
          $output->Text('</tr>');
          $hasEndTag = true;
        }
        else
          $hasEndTag = false;

        ++$pos;
      }

      if (!$hasEndTag)
        $output->Text('</tr>');
      $output->Text('</table>');
      $output->SetInputMode(_PNH_PARSEINPUT);
    }
    else
      $output->Text(_PTMP_PIXELNET_EMPTYALBUM);

    return $output->GetOutput();
  }


  // =====================================
  // End API, start private implementation
  // =====================================

  function showTitle(&$output, $title, $description, $folderID)
  {     
    $output->SetInputMode(_PNH_VERBATIMINPUT);

    if ($title != NULL  &&  $title != '')
    {
      $output->Text('<div class="photoshare-heading">' . pnVarPrepForDisplay($title) . '</div>');
      $output->Text("\n" . pnVarPrepHTMLDisplay($description) . "<hr>\n");
      $output->Text("<a href=\"".pnVarPrepHTMLDisplay(pnModURL('pixelnet', 'user', 'startsession'))."\">Warenkorb leeren</a><br>");
      $output->Text("Anzahl der Bilder im Warenkorb: " .pnSessionGetVar('pixelnet_imgno') );
      $output->Text("<br /><a href=\"".pnVarPrepHTMLDisplay(pnModURL('pixelnet', 'user', 'gotoshop'))."\">Warenkorb anzeigen</a><br />\n");
    }

    $output->SetInputMode(_PNH_PARSEINPUT);
  }


  function getThumbnailHTML($title, $id, $pos, $folderID, $templateHelper)
  {
    $imageURL   = $templateHelper->thumbnailURL($folderID, $id);
    $largerURL  = $templateHelper->folderURL($folderID, 'slideshow') . "&iid=$id";
    
    $html = "<table class=\"photoshare-image\">\n";

    $html .= "<tr><td style=\"text-align: center;\"><a href=\"$largerURL\"><img src=\"$imageURL\" id=\"$id\" border=\"0\" title=\""
             . _PTMP_PIXELNET_CLICKTOENLARGE . "\"></a></td></tr>";
    
    $html .= "<tr><td style=\"text-align: center;\" class=\"title\" align=\"center\">" . pnVarPrepForDisplay($title) . "</td></tr>\n";
    $addurl = pnVarPrepHTMLDisplay(pnModURL('pixelnet', 'user', 'addimage', array('pid' => $id)));
    $html .= "<tr><td style=\"text-align: center;\"><a href=\"$addurl\"><strong>Bestellen</strong></a></td></tr>";

    $html .= "</table>\n";

    return $html;
  }

}


function photoshare_template_pixelnet()
{
  return new PixelnetTemplate;
}

?>
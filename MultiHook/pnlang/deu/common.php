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

// even newer :-)
define('_MH_RELAXEDCENSORING', 'Beim Zensieren von Worten mit mehr als zwei Buchstaben L�nge den ersten und letzten Buchstaben nicht ersetzen');

define('_MH_NEEDLESOURCE', 'Quelle');
define('_MH_CENSORINWORDS', 'Zensierte W�rter innerhalb von Begriffen ersetzen, z.B. oo in Google ersetzen');

define('_MH_GOTOHOMEPAGE', 'gehe zur MultiHook-Projektseite im code.zikula.org');
define('_MH_LONGWITHHINT', 'Langfassung (im Fall eines Links, die Ziel-URL, wird bei unerw�nschten Begriffen ignoriert)');
define('_MH_TITLEWITHHINT', 'Titel (nur bei einem Link notwendig, wird bei unerw�nschten Begriffen ignoriert)');

define('_MH_FAVORITES', 'Favorites');
define('_MH_NEEDLES', 'Needles');
define('_MH_NOTHOOKED', '** der MultiHook wird zur Zeit von keinem Modul verwendet **');
define('_MH_LINKS', 'Links');
define('_MH_UPGRADETO50FAILED', 'Upgrade auf 5.0 fehlgeschlagen');
define('_MH_CENSOR', 'Zensur');
define('_MH_VIEWILLEGALWORDS', 'Unerw�nschte Begriffe');

// new
define('_MH_ISHOOKEDWITH', 'Der Multihook wird mit folgenden Modulen verwendet');
define('_MH_START','Start');
define('_MH_EXISTSINDB', 'bereits in der Datenbank vorhanden');
define('_MH_NONEEDLES', 'Keine Needles gefunden');
define('_MH_NODESCRIPTIONFOUND', 'keine Beschreibung vorhanden');
define('_MH_NEEDLEDATAERROR', 'Fehler beim Einlesen der Needledaten f�r \'%s\' oder Modul \'%s\' nicht aktiv');

//
// A
//
define('_MH_ABACFIRST', 'Gefundene Eintr�ge nur jeweils einmal im Text ersetzen');
define('_MH_ABBREVIATION', 'Abk�rzungen');
define('_MH_ACRONYM', 'Akronyme');
define('_MH_ADD', 'Eintrag hinzuf�gen');
define('_MH_ADDHOOK', 'Hook f�r weitere Module aktivieren');
define('_MH_ADMINTITLE','Links, Abk�rzungen, Akronyme, Zensur und Needles');

//
// C
//
define('_MH_CREATE', 'Erstellen');
define('_MH_CREATED', 'Eintrag erstellt');
define('_MH_CREATEFAILED','Anlegen des Eintrags fehlgeschlagen');

//
// D
//
define('_MH_DELETE', 'Eintrag l�schen');
define('_MH_DELETED', 'Eintrag gel�scht');
define('_MH_DELETEFAILED','L�schen des Eintrags fehlgeschlagen');

//
// E
//
define('_MH_ERRORREADINGDATA', 'Fehler beim Einlesen');
define('_MH_EXTERNALLINK', '(externer Link)');
define('_MH_EXTERNALLINKCLASS', 'CSS-Klasse f�r externe Links');

//
// G
//
define('_MH_GLOSSARY', 'Glossar');

//
// I
//
define('_MH_ITEMSPERPAGE', 'Anzahl der Eintr�ge pro Seite in der Adminanzeige');

//
// L
//
define('_MH_LANGUAGE','Sprache');
define('_MH_LANGUAGEEMPTY', 'Sprache fehlt');
define('_MH_LINK', 'Links');
define('_MH_LOADINGDATA', 'lade Daten...');
define('_MH_LONG','Langfassung');
define('_MH_LONGEMPTY', 'Langfassung fehlt');
define('_MH_LONGHINT', '(im Fall eines Links, die Ziel-URL, wird bei unerw�nschten Begriffen ignoriert)');

//
// M
//
define('_MH_MHINCODETAGS', 'MultiHook innerhalb [code][/code] benutzen');
define('_MH_MODIFYCONFIG', 'Konfiguration modifizieren');

//
// N
//
define('_MH_NOAUTH', 'Keine Berechtigung f�r das Multihook Modul');
define('_MH_NOITEMS', 'Keine Eintr�ge vorhanden');
define('_MH_NOSUCHITEM', 'Unbekannter Eintrag nicht vorhanden');

//
// O
//
define('_MH_OPTIONS', 'Optionen');

//
// R
//
define('_MH_REPLACEABBREVIATIONS', 'Abk�rzungen duch Langtext ersetzen');
define('_MH_REPLACELINKWITHTITLE', 'Links durch Titel ersetzen');

//
// S
//
define('_MH_SAVINGDATA', 'speichere Daten...');
define('_MH_SELECT','Ausw�hlen');
define('_MH_SELECTFAILED','MultiHook: Select auf die Datenbank fehlgeschlagen - bitte Admin verst�ndigen');
define('_MH_SHORT','Kurz');
define('_MH_SHORTEMPTY', 'Kurzform fehlt');
define('_MH_SHOWEDITLINK', 'Link zum Editieren anzeigen');
define('_MH_SHOWME','Anzeigen');

//
// T
//
define('_MH_TITLE','Titel');
define('_MH_TITLEEMPTY', 'Titel fehlt');
define('_MH_TITLEHINT', '(nur bei einem Link notwendig, wird bei unerw�nschten Begriffen ignoriert)');
define('_MH_TYPE','Typ');
define('_MH_TYPEABBREVIATION', 'Abk�rzung');
define('_MH_TYPEACRONYM', 'Akronym');
define('_MH_TYPENEEDLES', 'Needle');
define('_MH_TYPEILLEGALWORD', 'Unerw�nschter Begriff');
define('_MH_TYPEEMPTY', 'Typ fehlt');
define('_MH_TYPELINK', 'Link');

//
// U
//
define('_MH_UPDATECONFIG', 'Konfiguration aktualisieren');
define('_MH_UPDATED', 'Eintrag aktualisiert');
define('_MH_UPDATEDCONFIG', 'Konfiguration aktualisiert');
define('_MH_UPDATEFAILED','Aktualisierung des Eintrags fehlgeschlagen');

//
// V
//
define('_MH_VIEWABBR','Abk�rzungsliste');
define('_MH_VIEWACRONYMS','Akronymliste');
define('_MH_VIEWLINKS','Linkliste');
define('_MH_VIEWNEEDLES', 'Needleliste');
define('_MH_VIEWCENSOR', 'Liste der unerw�nschten Begriffe');

//
// W
//
define('_MH_WRONGPARAMETER_LONG', 'Keine Langversion oder (im Falle eines Links) keine URL angegeben');
define('_MH_WRONGPARAMETER_SHORT', 'Kurzbegriff nicht gew�hlt');
define('_MH_WRONGPARAMETER_TITLE', 'keine Titel angegeben');
define('_MH_WRONGPARAMETER_TYPE', 'ung�ltiger Typ');

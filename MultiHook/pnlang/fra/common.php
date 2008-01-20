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
define('_MH_NEEDLESOURCE', 'Source');
define('_MH_CENSORINWORDS', 'Remplace des mots interdits contenus dans d\'autres comme remplacer oo dans Google');

define('_MH_GOTOHOMEPAGE', 'rendez visite au projet MultiHook sur le NOC');
define('_MH_LONGWITHHINT', 'Version longue (dans le cas d\'un lien, l\'URL est ignoré pour les mots interdits)');
define('_MH_TITLEWITHHINT', 'Titre (nécessaire pour un lien, ignoré pour les mots interdits)');

define('_MH_FAVORITES', 'Favoris');
define('_MH_NEEDLES', 'Aiguillages');
define('_MH_NOTHOOKED', '** MultiHook n\'est utilisé dans aucun module **');
define('_MH_LINKS', 'Liens');
define('_MH_UPGRADETO50FAILED', 'La mise à jour vers la version 5.0 a échoué');
define('_MH_CENSOR', 'Censure');
define('_MH_VIEWILLEGALWORDS', 'Mots interdits');

// new
define('_MH_ISHOOKEDWITH', 'Actuellement Multihook est utilisé avec les modules suivants');
define('_MH_START','Débuter');
define('_MH_EXISTSINDB', ' déjà existant dans la base de données');
define('_MH_NONEEDLES', 'Aucun aiguillage trouvé');
define('_MH_NODESCRIPTIONFOUND', 'aucune description trouvée');
define('_MH_NEEDLEDATAERROR', 'Erreur de lecture des données d\'aiguillage pour \'%s\' ou le module \'%s\' n\'est pas actif');

//
// A
//
define('_MH_ABACFIRST', 'Remplacement de la première occurence seulement (par texte)');
define('_MH_ABBREVIATION', 'Abbreviations');
define('_MH_ACRONYM', 'Acronymes');
define('_MH_ADD','Ajouter un élément');
define('_MH_ADDHOOK', 'Activater MultiHook pour plus de modules');
define('_MH_ADMINTITLE','Liens, Abbreviations, Acronymes, Censure et Aiguillages');

//
// B
//
define('_MH_CREATE',        'Créer');
define('_MH_CREATED',       'élément ajouté');
define('_MH_CREATEFAILED',  'Erreur : Echec lors de la création de l\'élément');

//
// D
//

define('_MH_DELETE',        'Supprimer');
define('_MH_DELETED',       'Elément supprimé');
define('_MH_DELETEFAILED',  'Erreur lors de la suppression de l\'élément');

//
// E
//

define('_MH_ERRORREADINGDATA',  'Une erreur est survenue lors de la lecture des données');
define('_MH_EXTERNALLINK', '(lien externe)');
define('_MH_EXTERNALLINKCLASS', 'Définition ou classe CSS pour les liens externes');

//
// G
//

define('_MH_GLOSSARY', 'Glossaire');

//
// I
//
define('_MH_ITEMSPERPAGE', 'Eléments par page dans l\'administration');


//
// L
//
define('_MH_LANGUAGE',      'Langue');
define('_MH_LANGUAGEEMPTY', 'langue manquante');
define('_MH_LINK',          'Lien');
define('_MH_LOADINGDATA',   'Chargement des données...');
define('_MH_LONG',          'Texte long');
define('_MH_LONGEMPTY',     'Texte long manquant');
define('_MH_LONGHINT',      '(url en cas de lien)');

//
// M
//
define('_MH_MHINCODETAGS',  'Utiliser le MultiHook à l\'intérieur des balises [code][/code]');
define('_MH_MODIFYCONFIG',  'Modifier la Configuration');


//
// N
//
define('_MH_NOAUTH',      'L\'accès au module MultiHook vous est interdit');
define('_MH_NOITEMS',     'Aucune entrée');
define('_MH_NOSUCHITEM',  'Erreur : Elément inéxistant');


//
// O
//
define('_MH_OPTIONS','Options');

//
// R
//
define('_MH_REPLACEABBREVIATIONS', 'Remplacer les abréviations par le texte long');
define('_MH_REPLACELINKWITHTITLE', 'Remplacer les liens par leur titre');

//
// S
//
define('_MH_SAVINGDATA',    'enregistrement des données...');
define('_MH_SELECT',        'Selection');
define('_MH_SELECTFAILED',  'MultiHook: La sélection a échoué, svp. contactez l\'administrateur');
define('_MH_SHORT',         'Texte court');
define('_MH_SHORTEMPTY',    'Texte court manquant');
define('_MH_SHOWEDITLINK',  'Afficher le lien \'Modifier\'');
define('_MH_SHOWME',        'Afficher');

//
// T
//
define('_MH_TITLE',             'Titre');
define('_MH_TITLEEMPTY',        'titre manquant');
define('_MH_TITLEHINT',         '(nécessaire seulement en cas de lien)');
define('_MH_TYPE',              'Type');
define('_MH_TYPEABBREVIATION',  'Abréviation');
define('_MH_TYPEACRONYM',       'Acronyme');
define('_MH_TYPEEMPTY',         'Type manquant');
define('_MH_TYPELINK',          'Lien');
define('_MH_TYPENEEDLES', 'Aiguillage');
define('_MH_TYPEILLEGALWORD', 'Mot interdit');

//
// U
//

define('_MH_UPDATECONFIG',  'Mettre à jour la Configuration');
define('_MH_UPDATED',       'Elément ajouté');
define('_MH_UPDATEDCONFIG', 'Configuration mise à jour');
define('_MH_UPDATEFAILED',  'Erreur : Echec lors de la mise à jour de l\'élément');

//
// V
//
define('_MH_VIEWABBR',      'Voir les Abréviations');
define('_MH_VIEWACRONYMS',  'Voir les Acronymes');
define('_MH_VIEWLINKS',     'Voir les Liens');
define('_MH_VIEWNEEDLES', 'Voir les aiguillages');
define('_MH_VIEWCENSOR', 'Voir les mots censurés');

//
// W
//

define('_MH_WRONGPARAMETER_LONG',   'texte long ou (dans le cas d\'un lien), url manquant');
define('_MH_WRONGPARAMETER_SHORT',  'texte court manquant');
define('_MH_WRONGPARAMETER_TITLE',  'titre manquant');
define('_MH_WRONGPARAMETER_TYPE',   'type manquant');

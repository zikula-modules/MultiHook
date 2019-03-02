<?php
/**
 * MultiHook.
 *
 * @copyright Zikula Team (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Zikula Team <info@ziku.la>.
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.4.0 (https://modulestudio.de).
 */

namespace Zikula\MultiHookModule\HookProvider;

use Zikula\Bundle\HookBundle\Hook\FilterHook;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\MultiHookModule\HookProvider\Base\AbstractFilterHooksProvider;
use Zikula\MultiHookModule\Entity\Factory\EntityFactory;
use Zikula\MultiHookModule\Helper\HookHelper;
use Zikula\MultiHookModule\Helper\PermissionHelper;

/**
 * Implementation class for filter hooks provider.
 */
class FilterHooksProvider extends AbstractFilterHooksProvider
{
    /**
     * @var VariableApiInterface
     */
    private $variableApi;

    /**
     * @var EntityFactory
     */
    private $entityFactory;

    /**
     * @var HookHelper
     */
    private $hookHelper;

    public function setVariableApi(VariableApiInterface $variableApi)
    {
        $this->variableApi = $variableApi;
    }

    public function setEntityFactory(EntityFactory $entityFactory)
    {
        $this->entityFactory = $entityFactory;
    }

    public function setHookHelper(HookHelper $hookHelper)
    {
        $this->hookHelper = $hookHelper;
    }

    public function setPermissionHelper(PermissionHelper $permissionHelper)
    {
        $this->permissionHelper = $permissionHelper;
    }

    /**
     * @inheritDoc
     */
    public function applyFilter(FilterHook $hook)
    {
        // replace this by your own filter operation
        //parent::applyFilter($hook);

        $text = $hook->getData();
        //dump($text);

        // pad it with a space so we can distinguish between FALSE and matching the 1st char (index 0).
        $text = ' '  . $text;

        // add stylesheet
        //PageUtil::addVar('stylesheet', 'modules/MultiHook/style/mh.css');

        static $search = [];
        static $replace = [];
        static $finalsearch = [];
        static $finalreplace = [];
        static $gotAbbreviations = 0;
        static $gotNeedles = 0;
        
        static $mhAdmin;
        if (!isset($mhAdmin)) {
            $mhAdmin = $this->permissionHelper->hasPermission(ACCESS_DELETE);
        }

        $applyReplacementsToCodeTags = $this->variableApi->get('ZikulaMultiHookModule', 'applyReplacementsToCodeTags', false);
        $showEditLink = $mhAdmin && $this->variableApi->get('ZikulaMultiHookModule', 'showEditLink', true);
        $replaceOnlyFirstInstanceOfItems = $this->variableApi->get('ZikulaMultiHookModule', 'replaceOnlyFirstInstanceOfItems', false);
        $replaceCensoredWordsWhenTheyArePartOfOtherWords = $this->variableApi->get('ZikulaMultiHookModule', 'replaceCensoredWordsWhenTheyArePartOfOtherWords', false);
        $doNotCensorFirstAndLastLetterInWordsWithMoreThanTwoChars = $this->variableApi->get('ZikulaMultiHookModule', 'doNotCensorFirstAndLastLetterInWordsWithMoreThanTwoChars', false);

        //$needles = $this->variableApi->get('ZikulaMultiHookModule', 'MultiHook', 'needles', []);
        $needles = [];
        if (!is_array($needles)) {
            $needles =  [];
        }

        // deal with munded words (leet speak)
        $leetsearch  = ['/o/i', '/e/i', '/a/i', '/i/i'];
        $leetreplace = ['0', '3', '@', '1'];

        // current url and uri
        //$currenturl = System::getCurrentUrl();
        //$currenturi = System::getCurrentUri();

        $currenturl = '';
        $currenturi = '';
        $baseUrl = '';

        // Step 0 - remove areas that should not be changed, eg. for the zdebug plugin
        //          those areas are marked with <!--raw-->some hml<!--/raw-->
        $rawcount = preg_match_all("/<!--raw-->(.*)<!--\/raw-->/Usi", $text, $raws);
        for ($i = 0; $i < $rawcount; $i++) {
            $text = substr_replace($text, " MULTIHOOKRAWREPLACEMENT{$i} ", strpos($text, $raws[0][$i]), strlen($raws[0][$i]));
        }

        // Step 1 - move all bbcode with [code][/code] out of the way
        //          if MultiHook is configured accordingly
        if (false === $applyReplacementsToCodeTags) {
            // if we are faster than bbcode, we will have to remove the code tags
            $codecount1 = preg_match_all("/\[code(.*)\](.*)\[\/code\]/siU", $text, $codes1);
            for ($i = 0; $i < $codecount1; $i++) {
                $text = str_replace($codes1[0][$i], " MULTIHOOKCODE1REPLACEMENT{$i} ", $text);
                //$text = preg_replace('/(' . preg_quote($codes1[0][$i], '/') . ')/', " MULTIHOOKCODE1REPLACEMENT{$i} ", $text, 1);
            }
            // but pbbcode may have been faster than we are,. To avoid any problems its embraces the
            // replaced code tags with <!--code--> and <!--/code-->
            // this is what we are taking care of now
            $codecount2 = preg_match_all("/<!--code-->(.*)<!--\/code-->/siU", $text, $codes2);
            for ($i = 0; $i < $codecount2; $i++) {
                $text = str_replace($codes2[0][$i], " MULTIHOOKCODE2REPLACEMENT{$i} ", $text);
                //$text = preg_replace('/(' . preg_quote($codes2[0][$i], '/') . ')/', " MULTIHOOKCODE2REPLACEMENT{$i} ", $text, 1);
            }
        }

        // Step 2 - move all links out of the text and replace them with placeholders
        $tagcount = preg_match_all('/<a(.*)>(.*)<\/a>/siU', $text, $tags);
        for ($i = 0; $i < $tagcount; $i++) {
            $text = preg_replace('/(' . preg_quote($tags[0][$i], '/') . ')/', " MULTIHOOKTAGREPLACEMENT{$i} ", $text, 1);
        }

        // Step 3 - remove all html tags, we do not want to change them!!
        $htmlcount = preg_match_all("/<(?:[^\"\']+?|.+?(?:\"|\').*?(?:\"|\')?.*?)*?>/si", $text, $html);
        for ($i = 0; $i < $htmlcount; $i++) {
            $text = preg_replace('/(' . preg_quote($html[0][$i], '/') . ')/', " MULTIHOOKHTMLREPLACEMENT{$i} ", $text, 1);
        }

        // Step 4 - move all bbcode with [url][/url] out of the way
        $urlcount = preg_match_all("#\[url(.*)\](.*)\[\/url\]#siU", $text, $urls);
        for ($i = 0; $i < $urlcount; $i++) {
            $text = preg_replace('/(' . preg_quote($urls[0][$i], '/') . ')/', " MULTIHOOKURLREPLACEMENT{$i} ", $text, 1);
        }

        // Step 5 - move all urls starting with http:// etc. out of the way
        $linkcount = preg_match_all("/(http|https|ftp|ftps|news)\:\/\/([a-zA-Z0-9\-\._]+[\.]{1}[a-zA-Z]{2,6})(\/[a-zA-Z0-9\-\.\?\,\'\/\\\+&%\$#_=~]+)?/siU", $text, $links);
        for ($i = 0; $i < $linkcount; $i++) {
            $text = preg_replace('/(' . preg_quote($links[0][$i], '/') . ')/', " MULTIHOOKLINKREPLACEMENT{$i} ", $text, 1);
        }

        // Step 6 - move hilite hook additions out of the text
        $hilitecount = preg_match_all("/<!--hilite-->(.*)<!--\/hilite-->/siU", $text, $hilite);
        for ($i = 0; $i < $hilitecount; $i++) {
            $text = preg_replace('/(' . preg_quote($hilite[0][$i], '/') . ')/', " MULTIHOOKHILITEREPLACEMENT{$i} ", $text, 1);
        }

        if (empty($gotAbbreviations)) {
            $gotAbbreviations = 1;
            $entities = $this->entityFactory->getRepository('entry')->selectWhere('tbl.active = 1');
            // Create search/replace array from abbreviations/links information

            foreach ($entities as $entity) {
                $tmp = [
                    'id' => $entity->getId(),
                    'longform' => $entity->getLongForm(),
                    'shortform' => $entity->getShortForm(),
                    'title' => $entity->getTitle(),
                    'type' => $entity->getEntryType(),
                    'language' => $entity->getLocale()
                ];

                // check if the current tmp is a link
                //save original long
                $tmp['long_original'] = $tmp['longform'];
                if ($tmp['type'] == 2) {
                    $tmp['longform'] = $this->hookHelper->createAbsoluteUrl($tmp['longform'], $baseUrl);
                }

                $tmp['longform'] = preg_replace('/(\b)/', '\\1MULTIHOOKTEMPORARY', $tmp['longform']);
                $tmp['title'] = preg_replace('/(\b)/', '\\1MULTIHOOKTEMPORARY', $tmp['title']);

                if ($tmp['type'] == 0) {
                    // 0 = Abbreviation
                    $search_temp = '/(?<![\/\w@\.:])(' . preg_quote($tmp['shortform'], '/'). ')(?![\/\w@])(?!\.\w)/i';
                    $search[] = $search_temp;
                    $replace[] = md5($search_temp);
                    $finalsearch[] = '/' . preg_quote(md5($search_temp), '/') . '/';
                    $finalreplace[] = $this->hookHelper->createAbbr($tmp, $showEditLink);
                    unset($search_temp);
                } elseif ($tmp['type'] == 1) {
                    // 1 = Acronym
                    $search_temp = '/(?<![\/\w@\.:])(' . preg_quote($tmp['shortform'], '/'). ')(?![\/\w@])(?!\.\w)/i';
                    $search[] = $search_temp;
                    $replace[] = md5($search_temp);
                    $finalsearch[] = '/' . preg_quote(md5($search_temp), '/') . '/';
                    $finalreplace[] = $this->hookHelper->createAcronym($tmp, $showEditLink);
                    unset($search_temp);
                } elseif ($tmp['type'] == 2) {
                    // 2 = Link
                    // don't show link if the target is the current url
                    if ($tmp['long_original'] != $currenturl && $tmp['long_original'] != $currenturi) {
                        // if short beginns with a single ' we need another regexp to not check for \w
                        // this enables autolinks for german deppenapostrophs :-)
                        if ($tmp['shortform'][0] == '\'') {
                            $search_temp = '/(?<![\/@\.:-])(' . preg_quote($tmp['shortform'], '/'). ')(?![\/\w@-])(?!\.\w)/i';
                        } else {
                            $search_temp = '/(?<![\/\w@\.:-])(' . preg_quote($tmp['shortform'], '/'). ')(?![\/\w@:-])(?!\.\w)/i';
                        }
                        $search[] = $search_temp;
                        $replace[] = md5($search_temp);
                        $finalsearch[] = '/' . preg_quote(md5($search_temp), '/') . '/';
                        $finalreplace[] = $this->hookHelper->createLink($tmp, $showEditLink);
                        unset($search_temp);
                    }
                } elseif ($tmp['type'] == 3) {
                    // original censored word
                    if (false === $replaceCensoredWordsWhenTheyArePartOfOtherWords) {
                        $search_temp = '/(?<![\/\w@\.:])(' . preg_quote($tmp['shortform'], '/'). ')(?![\/\w@])(?!\.\w)/i';
                    } else {
                        $search_temp = '/(?)(' . preg_quote($tmp['shortform'], '/') . ')(?)/i';
                    }
                    $search[] = $search_temp;
                    $replace[] = md5($search_temp);
                    $finalsearch[] = '/' . preg_quote(md5($search_temp), '/') . '/';
                    $finalreplace[] = $this->hookHelper->createCensor($tmp, $showEditLink, $doNotCensorFirstAndLastLetterInWordsWithMoreThanTwoChars);

                    // Common replacements
                    $mungedword = preg_replace($leetsearch, $leetreplace, $tmp['shortform']);
                    if ($mungedword != $tmp['shortform']) {
                        $search_temp = '/(?<![\/\w@\.:])(' . preg_quote($mungedword, '/'). ')(?![\/\w@])(?!\.\w)/i';
                        $search[] = $search_temp;
                        $replace[] = md5($search_temp);
                        $finalsearch[] = '/' . preg_quote(md5($search_temp), '/') . '/';
                        $finalreplace[] = $this->hookHelper->createCensor($tmp, $showEditLink, $doNotCensorFirstAndLastLetterInWordsWithMoreThanTwoChars);
                    }
                    unset($search_temp);
                }
            } // foreach
        }

        for ($i = 0; $i < $linkcount; $i++) {
            $text = preg_replace("/ MULTIHOOKLINKREPLACEMENT{$i} /", $links[0][$i], $text, 1);
        }

        // check for needles
        // TODO migrate needles
        if (empty($gotNeedles)) {
            $gotNeedles = 1;
            /*
            if (count($needles) > 0) {
                foreach ($needles as $singleneedle) {
                    if (!is_array($singleneedle['needle'])) {
                        $singleneedle['needle'] = [$singleneedle['needle']];
                    }
                    $regexpmodifier = (isset($singleneedle['casesensitive']) && $singleneedle['casesensitive'] == false) ? 'i' : '';
                    foreach ($singleneedle['needle'] as $needle) {
                        preg_match_all('/(?<![\/\w@\.:])' . preg_quote(strtoupper($needle), '/') . '([a-zA-Z0-9\.\?\/&:=_-]*?)(?![\/\?\w&@:=_-])(?!\.\w)/' . $regexpmodifier, $text, $needleresults);
                        if (is_array($needleresults) && count($needleresults[0]) > 0) {
                            // complete needle in $needleresults[0], needle id in $needleresults[1]
                            // both are arrays!
                            for ($ncnt = 0; $ncnt < count($needleresults[0]); $ncnt++) {
                                $search_temp = '/(?<![\/\w@\.:])(' . preg_quote($needleresults[0][$ncnt], '/'). ')(?![\/\w@:-])(?!\.\w)/';
                                $search[]      = $search_temp;
                                $replace[]     = md5($search_temp);
                                $finalsearch[] = '/' . preg_quote(md5($search_temp), '/') . '/';
                                // TODO migrate needle call
                                $finalreplace[] = ModUtil::apiFunc(
                                    ($singleneedle['builtin'] == true)  ? 'ZikulaMultiHookModule' : $singleneedle['module'], 'needle', strtolower($singleneedle['function']),
                                        [
                                            'nid'    => $needleresults[1][$ncnt],
                                            'needle' => $needle
                                        ]
                                );
                                unset($search_temp);
                            }
                        }
                    }
                }
            }
            */
        }

        // Step 7 - the main replacements
        if (true === $replaceOnlyFirstInstanceOfItems) {
            $text = preg_replace($search, $replace, $text, 1);
            $text = preg_replace($finalsearch, $finalreplace, $text, 1);
        } else {
            $text = preg_replace($search, $replace, $text);
            $text = preg_replace($finalsearch, $finalreplace, $text);
        }

        // Step 8 - replace the spaces we munged in preparation of step 6
        $text = str_replace('MULTIHOOKTEMPORARY', '', $text);

        // Step 9-15 - replace the tags that we removed before
        for ($i = 0; $i < $hilitecount; $i++) {
            $text = preg_replace("/ MULTIHOOKHILITEREPLACEMENT{$i} /", $hilite[0][$i], $text, 1);
        }
        /*
        for ($i = 0; $i < $linkcount; $i++) {
            $text = preg_replace("/ MULTIHOOKLINKREPLACEMENT{$i} /", $links[0][$i], $text, 1);
        }
        */
        for ($i = 0; $i < $urlcount; $i++) {
            $text = preg_replace("/ MULTIHOOKURLREPLACEMENT{$i} /", $urls[0][$i], $text, 1);
        }
        for ($i = 0; $i < $htmlcount; $i++) {
            $text = preg_replace("/ MULTIHOOKHTMLREPLACEMENT{$i} /", $html[0][$i], $text, 1);
        }
        for ($i = 0; $i < $tagcount; $i++) {
            $text = preg_replace("/ MULTIHOOKTAGREPLACEMENT{$i} /", $tags[0][$i], $text, 1);
        }

        if (false === $applyReplacementsToCodeTags) {
            for ($i = 0; $i < $codecount2; $i++) {
                $text = str_replace(" MULTIHOOKCODE2REPLACEMENT{$i} ", $codes2[0][$i], $text);
                //$text = preg_replace("/ MULTIHOOKCODE2REPLACEMENT{$i} /", $codes2[0][$i], $text, 1);
            }
            for ($i = 0; $i < $codecount1; $i++) {
                $text = str_replace(" MULTIHOOKCODE1REPLACEMENT{$i} ", $codes1[0][$i], $text);
                //$text = preg_replace("/ MULTIHOOKCODE1REPLACEMENT{$i} /", $codes1[0][$i], $text, 1);
            }
        }

        for ($i = 0; $i < $rawcount; $i++) {
            $text = str_replace(" MULTIHOOKRAWREPLACEMENT{$i} ", $raws[0][$i], $text);
        }

        // Remove our padding from the string..
        $text = substr($text, 1);

        //dump($text);

        $hook->setData($text);
    }
}

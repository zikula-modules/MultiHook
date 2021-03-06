<?php

/**
 * MultiHook.
 *
 * @copyright Zikula Team (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Zikula Team <info@ziku.la>.
 *
 * @see https://ziku.la
 *
 * @version Generated by ModuleStudio 1.5.0 (https://modulestudio.de).
 */

declare(strict_types=1);

namespace Zikula\MultiHookModule\HookProvider;

use Symfony\Component\HttpFoundation\RequestStack;
use Zikula\Bundle\HookBundle\Hook\FilterHook;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\MultiHookModule\Collector\EntryProviderCollector;
use Zikula\MultiHookModule\Collector\NeedleCollector;
use Zikula\MultiHookModule\Helper\HookHelper;
use Zikula\MultiHookModule\Helper\PermissionHelper;
use Zikula\MultiHookModule\HookProvider\Base\AbstractFilterHooksProvider;
use Zikula\ThemeModule\Api\ApiInterface\PageAssetApiInterface;
use Zikula\ThemeModule\Engine\Asset;

/**
 * Implementation class for filter hooks provider.
 */
class FilterHooksProvider extends AbstractFilterHooksProvider
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var VariableApiInterface
     */
    private $variableApi;

    /**
     * @var EntryProviderCollector
     */
    private $entryProviderCollector;

    /**
     * @var NeedleCollector
     */
    private $needleCollector;

    /**
     * @var PermissionHelper
     */
    private $permissionHelper;

    /**
     * @var HookHelper
     */
    private $hookHelper;

    /**
     * @var PageAssetApiInterface
     */
    private $pageAssetApi;

    /**
     * @var Asset
     */
    private $assetHelper;

    public function applyFilter(FilterHook $hook): void
    {
        $request = $this->requestStack->getCurrentRequest();

        // check the user agent - if it is a bot, return immediately to avoid performance impact
        $robots = [
            'ia_archiver',
            'googlebot',
            'mediapartners-google',
            'yahoo!',
            'msnbot',
            'bingbot',
            'jeeves',
            'lycos',
        ];
        if (null !== $request) {
            $userAgent = $request->server->get('HTTP_USER_AGENT');
            foreach ($robots as $robot) {
                if (false !== mb_stripos($userAgent, $robot)) {
                    return;
                }
            }
        }

        // add custom styles (for older browsers)
        $this->pageAssetApi->add('stylesheet', $this->assetHelper->resolve('@ZikulaMultiHookModule:css/custom.css'));

        $text = $hook->getData();
        $callerId = md5($text);
        //dump($text);

        // pad it with a space so we can distinguish between FALSE and matching the 1st char (index 0).
        $text = ' ' . $text;

        static $search = [];
        static $replace = [];
        static $finalsearch = [];
        static $finalreplace = [];
        static $selectedEntries = [];
        static $gotAbbreviations = [];
        static $gotNeedles = [];

        if (!isset($gotAbbreviations[$callerId])) {
            $gotAbbreviations[$callerId] = 0;
        }
        if (!isset($gotNeedles[$callerId])) {
            $gotNeedles[$callerId] = 0;
        }

        static $mhAdmin;
        if (!isset($mhAdmin)) {
            $mhAdmin = $this->permissionHelper->hasPermission(ACCESS_DELETE);
        }

        $applyReplacementsToCodeTags = (bool) $this->variableApi->get(
            'ZikulaMultiHookModule',
            'applyReplacementsToCodeTags',
            false
        );
        $showEditLink = $mhAdmin && (bool) $this->variableApi->get(
            'ZikulaMultiHookModule',
            'showEditLink',
            true
        );
        $replaceOnlyFirstInstanceOfItems = (bool) $this->variableApi->get(
            'ZikulaMultiHookModule',
            'replaceOnlyFirstInstanceOfItems',
            false
        );
        $replaceCensoredWordsWhenTheyArePartOfOtherWords = (bool) $this->variableApi->get(
            'ZikulaMultiHookModule',
            'replaceCensoredWordsWhenTheyArePartOfOtherWords',
            false
        );
        $doNotCensorFirstAndLastLetterInWordsWithMoreThanTwoChars = (bool) $this->variableApi->get(
            'ZikulaMultiHookModule',
            'doNotCensorFirstAndLastLetterInWordsWithMoreThanTwoChars',
            false
        );

        $replaceAbbreviations = $this->variableApi->get('ZikulaMultiHookModule', 'replaceAbbreviations', true);
        $replaceAcronyms = $this->variableApi->get('ZikulaMultiHookModule', 'replaceAcronyms', true);
        $replaceLinks = $this->variableApi->get('ZikulaMultiHookModule', 'replaceLinks', true);
        $replaceCensoredWords = $this->variableApi->get('ZikulaMultiHookModule', 'replaceCensoredWords', true);
        $replaceNeedles = $this->variableApi->get('ZikulaMultiHookModule', 'replaceNeedles', true);

        $entryTypes = [];
        if (true === $replaceAbbreviations) {
            $entryTypes[] = 'abbr';
        }
        if (true === $replaceAcronyms) {
            $entryTypes[] = 'acronym';
        }
        if (true === $replaceLinks) {
            $entryTypes[] = 'link';
        }
        if (true === $replaceCensoredWords) {
            $entryTypes[] = 'censor';
        }

        // deal with munded words (leet speak)
        $leetsearch = ['/o/i', '/e/i', '/a/i', '/i/i'];
        $leetreplace = ['0', '3', '@', '1'];

        // base url for making links absolute if needed
        $baseUrl = $request->getSchemeAndHttpHost() . $request->getBasePath();

        // Step 0 - remove areas that should not be changed, eg. for the zdebug plugin
        //          those areas are marked with <!--raw-->some hml<!--/raw-->
        $rawcount = preg_match_all("/<!--raw-->(.*)<!--\/raw-->/Usi", $text, $raws);
        for ($i = 0; $i < $rawcount; ++$i) {
            $text = substr_replace(
                $text,
                " MULTIHOOKRAWREPLACEMENT{$i} ",
                mb_strpos($text, $raws[0][$i]),
                mb_strlen($raws[0][$i])
            );
        }

        $codes1 = $codes2 = [];
        // step 1 - move all bbcode with [code][/code] out of the way
        //          if MultiHook is configured accordingly
        if (false === $applyReplacementsToCodeTags) {
            // if we are faster than bbcode, we will have to remove the code tags
            preg_match_all("/\[code(.*)\](.*)\[\/code\]/siU", $text, $codes1);
            foreach ($codes1[0] as $i => $iValue) {
                $text = str_replace($codes1[0][$i], " MULTIHOOKCODE1REPLACEMENT{$i} ", $text);
                /*$text = preg_replace(
                    '/(' . preg_quote($codes1[0][$i], '/') . ')/',
                    " MULTIHOOKCODE1REPLACEMENT{$i} ",
                    $text,
                    1
                );*/
            }
            // but bbcode may have been faster than we are; to avoid any problems its embraces the
            // replaced code tags with <!--code--> and <!--/code-->
            // this is what we are taking care of now
            preg_match_all("/<!--code-->(.*)<!--\/code-->/siU", $text, $codes2);
            foreach ($codes2[0] as $i => $iValue) {
                $text = str_replace($codes2[0][$i], " MULTIHOOKCODE2REPLACEMENT{$i} ", $text);
                /*$text = preg_replace(
                    '/(' . preg_quote($codes2[0][$i], '/') . ')/',
                    " MULTIHOOKCODE2REPLACEMENT{$i} ",
                    $text,
                    1
                );*/
            }
        }

        // step 2 - move all links out of the text and replace them with placeholders
        $tagcount = preg_match_all('/<a(.*)>(.*)<\/a>/siU', $text, $tags);
        for ($i = 0; $i < $tagcount; ++$i) {
            $text = preg_replace(
                '/(' . preg_quote($tags[0][$i], '/') . ')/',
                " MULTIHOOKTAGREPLACEMENT{$i} ",
                $text,
                1
            );
        }

        // step 3 - remove all html tags, we do not want to change them!!
        $htmlcount = preg_match_all("/<(?:[^\"\']+?|.+?(?:\"|\').*?(?:\"|\')?.*?)*?>/si", $text, $html);
        for ($i = 0; $i < $htmlcount; ++$i) {
            $text = preg_replace(
                '/(' . preg_quote($html[0][$i], '/') . ')/',
                " MULTIHOOKHTMLREPLACEMENT{$i} ",
                $text,
                1
            );
        }

        // step 4 - move all bbcode with [url][/url] out of the way
        $urlcount = preg_match_all("#\[url(.*)\](.*)\[\/url\]#siU", $text, $urls);
        for ($i = 0; $i < $urlcount; ++$i) {
            $text = preg_replace(
                '/(' . preg_quote($urls[0][$i], '/') . ')/',
                " MULTIHOOKURLREPLACEMENT{$i} ",
                $text,
                1
            );
        }

        // step 5 - move all urls starting with http:// etc. out of the way
        $linkcount = preg_match_all(
            "/(http|https|ftp|ftps|news)\:\/\/"
                . "([a-zA-Z0-9\-\._]+[\.]{1}[a-zA-Z]{2,6})"
                . "(\/[a-zA-Z0-9\-\.\?\,\'\/\\\+&%\$#_=~]+)"
                . '?/siU',
            $text,
            $links
        );
        for ($i = 0; $i < $linkcount; ++$i) {
            $text = preg_replace(
                '/(' . preg_quote($links[0][$i], '/') . ')/',
                " MULTIHOOKLINKREPLACEMENT{$i} ",
                $text,
                1
            );
        }

        // step 6 - move hilite hook additions out of the text
        $hilitecount = preg_match_all("/<!--hilite-->(.*)<!--\/hilite-->/siU", $text, $hilite);
        for ($i = 0; $i < $hilitecount; ++$i) {
            $text = preg_replace(
                '/(' . preg_quote($hilite[0][$i], '/') . ')/',
                " MULTIHOOKHILITEREPLACEMENT{$i} ",
                $text,
                1
            );
        }

        if (empty($selectedEntries)) {
            foreach ($this->entryProviderCollector->getActive() as $entryProvider) {
                $providedEntries = $entryProvider->getEntries($entryTypes);
                foreach ($providedEntries as $entry) {
                    $selectedEntries[] = $entry;
                }
            }
        }
        if (empty($gotAbbreviations[$callerId])) {
            $gotAbbreviations[$callerId] = 1;
            $entries = $selectedEntries;

            // create search/replace array from abbreviations/links information
            foreach ($entries as $entry) {
                // check if the current tmp is a link
                //save original long
                $entry['long_original'] = $entry['longform'];
                if ('link' === $entry['type']) {
                    $entry['longform'] = $this->hookHelper->createAbsoluteUrl($entry['longform'], $baseUrl);
                }

                $entry['longform'] = preg_replace('/(\b)/', '\\1MULTIHOOKTEMPORARY', $entry['longform']);
                $entry['title'] = preg_replace('/(\b)/', '\\1MULTIHOOKTEMPORARY', $entry['title']);

                if ('abbr' === $entry['type']) {
                    $search_temp = '/(?<![\/\w@\.:])('
                        . preg_quote($entry['shortform'], '/')
                        . ')(?![\/\w@])(?!\.\w)/i'
                    ;
                    $search[] = $search_temp;
                    $replace[] = md5($search_temp);
                    $finalsearch[] = '/' . preg_quote(md5($search_temp), '/') . '/';
                    $finalreplace[] = $this->hookHelper->createAbbr($entry, $showEditLink);
                    unset($search_temp);
                } elseif ('acronym' === $entry['type']) {
                    $search_temp = '/(?<![\/\w@\.:])('
                        . preg_quote($entry['shortform'], '/')
                        . ')(?![\/\w@])(?!\.\w)/i'
                    ;
                    $search[] = $search_temp;
                    $replace[] = md5($search_temp);
                    $finalsearch[] = '/' . preg_quote(md5($search_temp), '/') . '/';
                    $finalreplace[] = $this->hookHelper->createAcronym($entry, $showEditLink);
                    unset($search_temp);
                } elseif ('link' === $entry['type']) {
                    // don't show link if the target is the current url
                    if (in_array($entry['long_original'], [$request->getUri(), $request->getRequestUri()], true)) {
                        continue;
                    }

                    // if short beginns with a single ' we need another regexp to not check for \w
                    // this enables autolinks for german deppenapostrophs :-)
                    if ('\'' === $entry['shortform'][0]) {
                        $search_temp = '/(?<![\/@\.:-])('
                            . preg_quote($entry['shortform'], '/')
                            . ')(?![\/\w@-])(?!\.\w)/i'
                        ;
                    } else {
                        $search_temp = '/(?<![\/\w@\.:-])('
                            . preg_quote($entry['shortform'], '/')
                            . ')(?![\/\w@:-])(?!\.\w)/i'
                        ;
                    }
                    $search[] = $search_temp;
                    $replace[] = md5($search_temp);
                    $finalsearch[] = '/' . preg_quote(md5($search_temp), '/') . '/';
                    $finalreplace[] = $this->hookHelper->createLink($entry, $showEditLink);
                    unset($search_temp);
                } elseif ('censor' === $entry['type']) {
                    // original censored word
                    if (false === $replaceCensoredWordsWhenTheyArePartOfOtherWords) {
                        $search_temp = '/(?<![\/\w@\.:])('
                            . preg_quote($entry['shortform'], '/')
                            . ')(?![\/\w@])(?!\.\w)/i'
                        ;
                    } else {
                        $search_temp = '/(?)('
                            . preg_quote($entry['shortform'], '/')
                            . ')(?)/i'
                        ;
                    }
                    $search[] = $search_temp;
                    $replace[] = md5($search_temp);
                    $finalsearch[] = '/' . preg_quote(md5($search_temp), '/') . '/';
                    $finalreplace[] = $this->hookHelper->createCensor(
                        $entry,
                        $showEditLink,
                        $doNotCensorFirstAndLastLetterInWordsWithMoreThanTwoChars
                    );

                    // common replacements
                    $mungedword = preg_replace($leetsearch, $leetreplace, $entry['shortform']);
                    if ($mungedword !== $entry['shortform']) {
                        $search_temp = '/(?<![\/\w@\.:])('
                            . preg_quote($mungedword, '/')
                            . ')(?![\/\w@])(?!\.\w)/i'
                        ;
                        $search[] = $search_temp;
                        $replace[] = md5($search_temp);
                        $finalsearch[] = '/' . preg_quote(md5($search_temp), '/') . '/';
                        $finalreplace[] = $this->hookHelper->createCensor(
                            $entry,
                            $showEditLink,
                            $doNotCensorFirstAndLastLetterInWordsWithMoreThanTwoChars
                        );
                    }
                    unset($search_temp);
                }
            }
        }

        for ($i = 0; $i < $linkcount; ++$i) {
            $text = preg_replace("/ MULTIHOOKLINKREPLACEMENT{$i} /", $links[0][$i], $text, 1);
        }

        if (true === $replaceNeedles) {
            // check for needles
            if (empty($gotNeedles[$callerId])) {
                $gotNeedles[$callerId] = 1;
                $needles = $this->needleCollector->getActive();
                if (count($needles) > 0) {
                    foreach ($needles as $needle) {
                        $subjects = method_exists($needle, 'getSubjects') ? $needle->getSubjects() : [];
                        if (!is_array($subjects)) {
                            $subjects = [$subjects];
                        }
                        if (empty($subjects) && method_exists($needle, 'getName')) {
                            $subjects[] = $needle->getName();
                        }
                        $regExpModifier = method_exists($needle, 'isCaseSensitive')
                            && false === $needle->isCaseSensitive()
                            ? 'i'
                            : ''
                        ;
                        foreach ($subjects as $subject) {
                            $search = '/(?<![\/\w@\.:])'
                                . preg_quote(mb_strtoupper($subject), '/')
                                . '([a-zA-Z0-9\.\?\/&:=_-]*?)(?![\/\?\w&@:=_-])(?!\.\w)/'
                                . $regExpModifier
                            ;
                            preg_match_all($search, $text, $needleResults);
                            if (is_array($needleResults) && count($needleResults[0]) > 0) {
                                // complete needle in $needleResults[0], needle id in $needleResults[1]
                                // both are arrays
                                $amountOfNeedleResults = count($needleResults[0]);
                                for ($ncnt = 0; $ncnt < $amountOfNeedleResults; ++$ncnt) {
                                    $search_temp = '/(?<![\/\w@\.:])('
                                        . preg_quote($needleResults[0][$ncnt], '/')
                                        . ')(?![\/\w@:-])(?!\.\w)/'
                                    ;
                                    $search[] = $search_temp;
                                    $replace[] = md5($search_temp);
                                    $finalsearch[] = '/' . preg_quote(md5($search_temp), '/') . '/';

                                    $finalreplace[] = $needle->apply($needleResults[1][$ncnt], $subject);
                                    unset($search_temp);
                                }
                            }
                        }
                    }
                }
            }
        }

        // step 7 - the main replacements
        if (true === $replaceOnlyFirstInstanceOfItems) {
            $text = preg_replace($search, $replace, $text, 1);
            $text = preg_replace($finalsearch, $finalreplace, $text, 1);
        } else {
            $text = preg_replace($search, $replace, $text);
            $text = preg_replace($finalsearch, $finalreplace, $text);
        }

        // step 8 - replace the spaces we munged in preparation of step 6
        $text = str_replace('MULTIHOOKTEMPORARY', '', $text);

        // step 9-15 - replace the tags that we removed before
        for ($i = 0; $i < $hilitecount; ++$i) {
            $text = preg_replace("/ MULTIHOOKHILITEREPLACEMENT{$i} /", $hilite[0][$i], $text, 1);
        }
        /*
        for ($i = 0; $i < $linkcount; ++$i) {
            $text = preg_replace("/ MULTIHOOKLINKREPLACEMENT{$i} /", $links[0][$i], $text, 1);
        }
        */
        for ($i = 0; $i < $urlcount; ++$i) {
            $text = preg_replace("/ MULTIHOOKURLREPLACEMENT{$i} /", $urls[0][$i], $text, 1);
        }
        for ($i = 0; $i < $htmlcount; ++$i) {
            $text = preg_replace("/ MULTIHOOKHTMLREPLACEMENT{$i} /", $html[0][$i], $text, 1);
        }
        for ($i = 0; $i < $tagcount; ++$i) {
            $text = preg_replace("/ MULTIHOOKTAGREPLACEMENT{$i} /", $tags[0][$i], $text, 1);
        }

        if (false === $applyReplacementsToCodeTags) {
            foreach ($codes2[0] as $i => $iValue) {
                $text = str_replace(" MULTIHOOKCODE2REPLACEMENT{$i} ", $codes2[0][$i], $text);
                //$text = preg_replace("/ MULTIHOOKCODE2REPLACEMENT{$i} /", $codes2[0][$i], $text, 1);
            }
            foreach ($codes1[0] as $i => $iValue) {
                $text = str_replace(" MULTIHOOKCODE1REPLACEMENT{$i} ", $codes1[0][$i], $text);
                //$text = preg_replace("/ MULTIHOOKCODE1REPLACEMENT{$i} /", $codes1[0][$i], $text, 1);
            }
        }

        for ($i = 0; $i < $rawcount; ++$i) {
            $text = str_replace(" MULTIHOOKRAWREPLACEMENT{$i} ", $raws[0][$i], $text);
        }

        // remove our padding from the string
        $text = mb_substr($text, 1);

        //dump($text);
        $hook->setData($text);
    }

    /**
     * @required
     */
    public function setRequestStack(RequestStack $requestStack): void
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @required
     */
    public function setVariableApi(VariableApiInterface $variableApi): void
    {
        $this->variableApi = $variableApi;
    }

    /**
     * @required
     */
    public function setEntryProviderCollector(EntryProviderCollector $entryProviderCollector): void
    {
        $this->entryProviderCollector = $entryProviderCollector;
    }

    /**
     * @required
     */
    public function setNeedleCollector(NeedleCollector $needleCollector): void
    {
        $this->needleCollector = $needleCollector;
    }

    /**
     * @required
     */
    public function setHookHelper(HookHelper $hookHelper): void
    {
        $this->hookHelper = $hookHelper;
    }

    /**
     * @required
     */
    public function setPermissionHelper(PermissionHelper $permissionHelper): void
    {
        $this->permissionHelper = $permissionHelper;
    }

    /**
     * @required
     */
    public function setPageAssetApi(PageAssetApiInterface $pageAssetApi): void
    {
        $this->pageAssetApi = $pageAssetApi;
    }

    /**
     * @required
     */
    public function setAssetHelper(Asset $assetHelper): void
    {
        $this->assetHelper = $assetHelper;
    }
}

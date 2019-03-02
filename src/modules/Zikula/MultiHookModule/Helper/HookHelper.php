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

namespace Zikula\MultiHookModule\Helper;

use Symfony\Component\Routing\RouterInterface;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\MultiHookModule\Helper\Base\AbstractHookHelper;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;

/**
 * Helper implementation class for hook related methods.
 */
class HookHelper extends AbstractHookHelper
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @var VariableApiInterface
     */
    private $variableApi;    

    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }

    public function setRouter(RouterInterface $router)
    {
        $this->router = $router;
    }
    
    public function setVariableApi(VariableApiInterface $variableApi)
    {
        $this->variableApi = $variableApi;
    }
    
    public function createAbbr($abac, $showEditLink=false)
    {
        $replaceAbbreviationsWithLongText = $this->variableApi->get('ZikulaMultiHookModule', 'replaceAbbreviationsWithLongText', false);

        $long = $abac['longform'];
        $short = $abac['shortform'];
        $id = $abac['id'];

        $replace_temp = '';
        if (false === $replaceAbbreviationsWithLongText) {
            $xhtmllang = $this->getLanguageAttributes($abac['language']);
            $replace_temp = '<abbr' . $xhtmllang . ' title="' . str_replace('"', '', $long) . '"><span class="abbr" title="' . str_replace('"', '', $long) . '">' . $short . '</span></abbr>';
        } else {
            $replace_temp = $long;
        }

        if (true === $showEditLink) {
            $replace_temp = '<span>' . $replace_temp . ' ' . $this->getEditLink($short, $this->translator->__('Abbreviation', 'zikulamultihookmodule'), $id) . '</span>';
        }

        return $replace_temp;
    }
    
    public function createAcronym($abac, $showEditLink=false)
    {
        $long = $abac['longform'];
        $short = $abac['shortform'];
        $id = $abac['id'];

        $xhtmllang = $this->getLanguageAttributes($abac['language']);
        $replace_temp = '<acronym' . $xhtmllang . ' title="' . str_replace('"', '', $long) . '">' . $short . '</acronym>';

        if (true === $showEditLink) {
            $replace_temp = '<span>' . $replace_temp . ' ' . $this->getEditLink($short, $this->translator->__('Acronym', 'zikulamultihookmodule'), $id) . '</span>';
        }

        return $replace_temp;
    }

    public function createLink($abac, $showEditLink=false)
    {
        $replaceLinksWithTitle = $this->variableApi->get('ZikulaMultiHookModule', 'replaceLinksWithTitle', false);
        $cssClassForExternalLinks = $this->variableApi->get('ZikulaMultiHookModule', 'cssClassForExternalLinks', '');

        $extclass = '';
        $accessibilityHack = '';
        if (preg_match("/(^http:\/\/)/", $abac['longform']) == 1) {
            if (!empty($cssClassForExternalLinks)) {
                $extclass = ' class="' . $cssClassForExternalLinks . '"';
            }
            $accessibilityHack = ''; // not working yet: <span class="mhacconly"> ' . str_replace('"', '', __('(external link)', $dom)) . '</span>';
        }

        $long  = $abac['longform'];
        $short = $abac['shortform'];
        $id = $abac['id'];
        $title = $abac['title'];

        $linkText = (false === $replaceLinksWithTitle ? $short : $title) . $accessibilityHack;
        $replace_temp = '<a' . $extclass . ' href="' . str_replace('"', '', $long) . '" title="' . str_replace('"', '', $title) . '">' . $linkText . '</a>';

        if (true === $showEditLink) {
            $replace_temp = '<span>' . $replace_temp . ' ' . $this->getEditLink($short, $this->translator->__('Link', 'zikulamultihookmodule'), $id) . '</span>';
        }

        return $replace_temp;
    }

    public function createCensor($abac, $showEditLink=false, $doNotCensorFirstAndLastLetterInWordsWithMoreThanTwoChars=false)
    {
        $short = $abac['shortform'];

        $len = strlen($short);
        $replace_temp = str_repeat('*', $len);
        if (true === $doNotCensorFirstAndLastLetterInWordsWithMoreThanTwoChars && $len > 2) {
            $replace_temp[0] = $short[0];
            $id = strlen($replace_temp)-1;
            $replace_temp[$id] = $short[$len-1];
        }

        $id = $abac['id'];

        if (true === $showEditLink) {
            $replace_temp = '<span>' . $replace_temp . ' ' . $this->getEditLink($short, $this->translator->__('Censor', 'zikulamultihookmodule'), $id) . '</span>';
        }

        return $replace_temp;
    }

    private function getLanguageAttributes($lang)
    {
        return !empty($lang) ? ' lang="' . $lang . '" xml:lang="' . $lang . '"' : '';
    }

    public function getEditLink($short, $entryLabel = '', $id = 0)
    {
        $title = $this->translator->__('Edit', 'zikulamultihookmodule') . ': ' . $short . ' (' . str_replace('"', '', $entryLabel) . ') #' . $id;

        return '<a href="' . $this->router->generate('zikulamultihookmodule_entry_edit', ['id' => $id]) . '" class="mh-edit-link" title="' . str_replace('"', '', $title) . '" target="_blank"><i class="fa fa-pencil"></i></a>';
    }

    public function createAbsoluteUrl($url='', $baseUrl='')
    {
        static $schemes = ['http', 'https', 'ftp', 'gopher', 'ed2k', 'news', 'mailto', 'telnet'];

        if (strlen($url) == 0) {
            return $url;
        }

        // make sure that relative urls get converted to absolute urls (safehtml needs this)
        $exploded_url = explode(':', $url);
        if (!in_array($exploded_url[0], $schemes)) {
            // url does not start with one of the schemes defined above
            // we consider it being a relative path now

            // next check for leading / in  relative url
            if ($url[0] == '/') {
                // and remove it
                $url = substr($url, 1);
            }
            $url = $baseUrl . $url;
        }

        return $url;
    }
}

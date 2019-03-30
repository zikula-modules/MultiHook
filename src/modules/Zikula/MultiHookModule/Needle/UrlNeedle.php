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

namespace Zikula\MultiHookModule\Needle;

use Symfony\Component\HttpFoundation\RequestStack;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;

/**
 * URL needle
 */
class UrlNeedle
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var string
     */
    private $cssClassForExternalLinks;

    /**
     * Bundle name
     *
     * @var string
     */
    private $bundleName;

    /**
     * The name of this needle
     *
     * @var string
     */
    private $name;

    public function __construct(
        TranslatorInterface $translator,
        RequestStack $requestStack,
        VariableApiInterface $variableApi
    ) {
        $this->translator = $translator;
        $this->requestStack = $requestStack;
        $this->cssClassForExternalLinks = $variableApi->get('ZikulaMultiHookModule', 'cssClassForExternalLinks', '');

        $nsParts = explode('\\', get_class($this));
        $vendor = $nsParts[0];
        $nameAndType = $nsParts[1];

        $this->bundleName = $vendor . $nameAndType;
        $this->name = str_replace('Needle', '', array_pop($nsParts));
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getIcon(): string
    {
        return 'globe';
    }

    public function getTitle(): string
    {
        return $this->translator->__('URLs', 'zikulamultihookmodule');
    }

    public function getDescription(): string
    {
        return $this->translator->__('Makes URLs clickable, works with http, https, ftp and mailto URLs.', 'zikulamultihookmodule');
    }

    public function getUsageInfo(): string
    {
        return $this->translator->__('https://www.example.com', 'zikulamultihookmodule');
    }

    public function isActive(): bool
    {
        return true;
    }

    public function isCaseSensitive(): bool
    {
        return false;
    }

    public function getSubjects(): array
    {
        return ['http://', 'https://', 'ftp://', 'mailto://'];
    }

    public function apply(string $needleId, string $needleText): string
    {
        if (empty($needleId)) {
            return $needleId;
        }

        $request = $this->requestStack->getCurrentRequest();
        $baseUrl = $request->getSchemeAndHttpHost() . $request->getBasePath();

        // simple replacement, no need to cache anything
        $url = htmlspecialchars($needleText . $needleId);

        $extclass = '';
        if (false === stripos($baseUrl, $url)) {
            if (!empty($this->cssClassForExternalLinks)) {
                $extclass = ' class="' . $this->cssClassForExternalLinks . '"';
            }
        }

        return '<a href="' . $url . '"' . $extclass . '>' . $url . '</a>';
    }

    public function getBundleName(): string
    {
        return $this->bundleName;
    }
}

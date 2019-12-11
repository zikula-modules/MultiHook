<?php

/**
 * MultiHook.
 *
 * @copyright Zikula Team (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Zikula Team <info@ziku.la>.
 * @see https://ziku.la
 * @version Generated by ModuleStudio 1.4.0 (https://modulestudio.de).
 */

declare(strict_types=1);

namespace Zikula\MultiHookModule\Container\Base;

use Symfony\Component\Routing\RouterInterface;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\Core\LinkContainer\LinkContainerInterface;
use Zikula\MultiHookModule\Helper\ControllerHelper;
use Zikula\MultiHookModule\Helper\PermissionHelper;

/**
 * This is the link container service implementation class.
 */
abstract class AbstractLinkContainer implements LinkContainerInterface
{
    use TranslatorTrait;

    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * @var ControllerHelper
     */
    protected $controllerHelper;

    /**
     * @var PermissionHelper
     */
    protected $permissionHelper;

    public function __construct(
        TranslatorInterface $translator,
        RouterInterface $router,
        ControllerHelper $controllerHelper,
        PermissionHelper $permissionHelper
    ) {
        $this->setTranslator($translator);
        $this->router = $router;
        $this->controllerHelper = $controllerHelper;
        $this->permissionHelper = $permissionHelper;
    }

    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    public function getLinks(string $type = LinkContainerInterface::TYPE_ADMIN): array
    {
        $contextArgs = ['api' => 'linkContainer', 'action' => 'getLinks'];
        $allowedObjectTypes = $this->controllerHelper->getObjectTypes('api', $contextArgs);

        $permLevel = LinkContainerInterface::TYPE_ADMIN === $type ? ACCESS_ADMIN : ACCESS_READ;

        // Create an array of links to return
        $links = [];

        if (LinkContainerInterface::TYPE_ACCOUNT === $type) {

            return $links;
        }

        $routeArea = LinkContainerInterface::TYPE_ADMIN === $type ? 'admin' : '';
        if (LinkContainerInterface::TYPE_ADMIN === $type) {
            if ($this->permissionHelper->hasPermission(ACCESS_READ)) {
                $links[] = [
                    'url' => $this->router->generate('zikulamultihookmodule_entry_index'),
                    'text' => $this->__('Frontend', 'zikulamultihookmodule'),
                    'title' => $this->__('Switch to user area.', 'zikulamultihookmodule'),
                    'icon' => 'home'
                ];
            }
        } else {
            if ($this->permissionHelper->hasPermission(ACCESS_ADMIN)) {
                $links[] = [
                    'url' => $this->router->generate('zikulamultihookmodule_entry_adminindex'),
                    'text' => $this->__('Backend', 'zikulamultihookmodule'),
                    'title' => $this->__('Switch to administration area.', 'zikulamultihookmodule'),
                    'icon' => 'wrench'
                ];
            }
        }
        
        if (in_array('entry', $allowedObjectTypes, true)
            && $this->permissionHelper->hasComponentPermission('entry', $permLevel)) {
            $links[] = [
                'url' => $this->router->generate('zikulamultihookmodule_entry_' . $routeArea . 'view'),
                'text' => $this->__('Entries', 'zikulamultihookmodule'),
                'title' => $this->__('Entries list', 'zikulamultihookmodule')
            ];
        }
        if ('admin' === $routeArea && $this->permissionHelper->hasPermission(ACCESS_ADMIN)) {
            $links[] = [
                'url' => $this->router->generate('zikulamultihookmodule_config_config'),
                'text' => $this->__('Settings', 'zikulamultihookmodule'),
                'title' => $this->__('Manage settings for this application', 'zikulamultihookmodule'),
                'icon' => 'wrench'
            ];
        }

        return $links;
    }

    public function getBundleName(): string
    {
        return 'ZikulaMultiHookModule';
    }
}

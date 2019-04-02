<?php

declare(strict_types=1);

/**
 * MultiHook.
 *
 * @copyright Zikula Team (Zikula)
 * @license http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public License
 * @author Zikula Team <info@ziku.la>.
 * @link https://ziku.la
 * @version Generated by ModuleStudio 1.4.0 (https://modulestudio.de).
 */

namespace Zikula\MultiHookModule\Menu\Base;

use Knp\Menu\FactoryInterface;
use Knp\Menu\ItemInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\UsersModule\Constant as UsersConstant;
use Zikula\MultiHookModule\Entity\EntryEntity;
use Zikula\MultiHookModule\MultiHookEvents;
use Zikula\MultiHookModule\Event\ConfigureItemActionsMenuEvent;
use Zikula\MultiHookModule\Helper\PermissionHelper;
use Zikula\UsersModule\Api\ApiInterface\CurrentUserApiInterface;

/**
 * Menu builder base class.
 */
class AbstractMenuBuilder
{
    use TranslatorTrait;

    /**
     * @var FactoryInterface
     */
    protected $factory;

    /**
     * @var EventDispatcherInterface
     */
    protected $eventDispatcher;

    /**
     * @var RequestStack
     */
    protected $requestStack;

    /**
     * @var PermissionHelper
     */
    protected $permissionHelper;

    /**
     * @var CurrentUserApiInterface
     */
    protected $currentUserApi;

    public function __construct(
        TranslatorInterface $translator,
        FactoryInterface $factory,
        EventDispatcherInterface $eventDispatcher,
        RequestStack $requestStack,
        PermissionHelper $permissionHelper,
        CurrentUserApiInterface $currentUserApi
    ) {
        $this->setTranslator($translator);
        $this->factory = $factory;
        $this->eventDispatcher = $eventDispatcher;
        $this->requestStack = $requestStack;
        $this->permissionHelper = $permissionHelper;
        $this->currentUserApi = $currentUserApi;
    }

    public function setTranslator(TranslatorInterface $translator): void
    {
        $this->translator = $translator;
    }

    /**
     * Builds the item actions menu.
     */
    public function createItemActionsMenu(array $options = []): ItemInterface
    {
        $menu = $this->factory->createItem('itemActions');
        if (!isset($options['entity'], $options['area'], $options['context'])) {
            return $menu;
        }

        $entity = $options['entity'];
        $routeArea = $options['area'];
        $context = $options['context'];
        $menu->setChildrenAttribute('class', 'list-inline item-actions');

        $this->eventDispatcher->dispatch(MultiHookEvents::MENU_ITEMACTIONS_PRE_CONFIGURE, new ConfigureItemActionsMenuEvent($this->factory, $menu, $options));

        if ($entity instanceof EntryEntity) {
            $routePrefix = 'zikulamultihookmodule_entry_';
        
            if ($this->permissionHelper->mayEdit($entity)) {
                $title = $this->__('Edit', 'zikulamultihookmodule');
                $menu->addChild($title, [
                    'route' => $routePrefix . $routeArea . 'edit',
                    'routeParameters' => $entity->createUrlArgs()
                ]);
                $menu[$title]->setLinkAttribute('title', $this->__('Edit this entry', 'zikulamultihookmodule'));
                $menu[$title]->setAttribute('icon', 'fa fa-pencil-square-o');
                $title = $this->__('Reuse', 'zikulamultihookmodule');
                $menu->addChild($title, [
                    'route' => $routePrefix . $routeArea . 'edit',
                    'routeParameters' => ['astemplate' => $entity->getKey()]
                ]);
                $menu[$title]->setLinkAttribute('title', $this->__('Reuse for new entry', 'zikulamultihookmodule'));
                $menu[$title]->setAttribute('icon', 'fa fa-files-o');
            }
        }

        $this->eventDispatcher->dispatch(MultiHookEvents::MENU_ITEMACTIONS_POST_CONFIGURE, new ConfigureItemActionsMenuEvent($this->factory, $menu, $options));

        return $menu;
    }
}

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

namespace Zikula\MultiHookModule\Listener\Base;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Zikula\Core\Event\GenericEvent;

/**
 * Event handler base class for dispatching modules.
 */
abstract class AbstractModuleDispatchListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            'module_dispatch.service_links' => ['serviceLinks', 5]
        ];
    }
    
    /**
     * Listener for the `module_dispatch.service_links` event.
     *
     * Occurs when building admin menu items.
     * Adds sublinks to a Services menu that is appended to all modules if populated.
     * Triggered by module_dispatch.postexecute in bootstrap.
     *
     * Inject router and translator services and format data like this:
     *     `$event->data[] = [
     *         'url' => $router->generate('zikulamultihookmodule_user_index'),
     *         'text' => $translator->trans('Link text')
     *     ];`
     *
     * You can access general data available in the event.
     *
     * The event name:
     *     `echo 'Event: ' . $event->getName();`
     *
     */
    public function serviceLinks(GenericEvent $event): void
    {
    }
}

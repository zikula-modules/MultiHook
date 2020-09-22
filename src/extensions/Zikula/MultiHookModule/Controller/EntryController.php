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

namespace Zikula\MultiHookModule\Controller;

use Zikula\MultiHookModule\Controller\Base\AbstractEntryController;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\RouterInterface;
use Zikula\ThemeModule\Engine\Annotation\Theme;
use Zikula\UsersModule\Api\ApiInterface\CurrentUserApiInterface;
use Zikula\MultiHookModule\Entity\EntryEntity;
use Zikula\MultiHookModule\Entity\Factory\EntityFactory;
use Zikula\MultiHookModule\Form\Handler\Entry\EditHandler;
use Zikula\MultiHookModule\Helper\ControllerHelper;
use Zikula\MultiHookModule\Helper\HookHelper;
use Zikula\MultiHookModule\Helper\PermissionHelper;
use Zikula\MultiHookModule\Helper\ViewHelper;
use Zikula\MultiHookModule\Helper\WorkflowHelper;

/**
 * Entry controller class providing navigation and interaction functionality.
 */
class EntryController extends AbstractEntryController
{
    
    /**
     * @Route("/admin/entries",
     *        methods = {"GET"}
     * )
     * @Theme("admin")
     */
    public function adminIndex(
        Request $request,
        PermissionHelper $permissionHelper
    ): Response {
        return $this->indexInternal(
            $request,
            $permissionHelper,
            true
        );
    }
    
    /**
     * @Route("/entries",
     *        methods = {"GET"}
     * )
     */
    public function index(
        Request $request,
        PermissionHelper $permissionHelper
    ): Response {
        return $this->indexInternal(
            $request,
            $permissionHelper,
            false
        );
    }

    /**
     * @Route("/admin/entries/view/{sort}/{sortdir}/{page}/{num}.{_format}",
     *        requirements = {"sortdir" = "asc|desc|ASC|DESC", "page" = "\d+", "num" = "\d+", "_format" = "html"},
     *        defaults = {"sort" = "", "sortdir" = "asc", "page" = 1, "num" = 10, "_format" = "html"},
     *        methods = {"GET"}
     * )
     * @Theme("admin")
     */
    public function adminView(
        Request $request,
        RouterInterface $router,
        PermissionHelper $permissionHelper,
        ControllerHelper $controllerHelper,
        ViewHelper $viewHelper,
        string $sort,
        string $sortdir,
        int $page,
        int $num
    ): Response {
        return $this->viewInternal(
            $request,
            $router,
            $permissionHelper,
            $controllerHelper,
            $viewHelper,
            $sort,
            $sortdir,
            $page,
            $num,
            true
        );
    }
    
    /**
     * @Route("/entries/view/{sort}/{sortdir}/{page}/{num}.{_format}",
     *        requirements = {"sortdir" = "asc|desc|ASC|DESC", "page" = "\d+", "num" = "\d+", "_format" = "html"},
     *        defaults = {"sort" = "", "sortdir" = "asc", "page" = 1, "num" = 10, "_format" = "html"},
     *        methods = {"GET"}
     * )
     */
    public function view(
        Request $request,
        RouterInterface $router,
        PermissionHelper $permissionHelper,
        ControllerHelper $controllerHelper,
        ViewHelper $viewHelper,
        string $sort,
        string $sortdir,
        int $page,
        int $num
    ): Response {
        return $this->viewInternal(
            $request,
            $router,
            $permissionHelper,
            $controllerHelper,
            $viewHelper,
            $sort,
            $sortdir,
            $page,
            $num,
            false
        );
    }

    /**
     * @Route("/admin/entry/edit/{id}.{_format}",
     *        requirements = {"id" = "\d+", "_format" = "html"},
     *        defaults = {"id" = "0", "_format" = "html"},
     *        methods = {"GET", "POST"}
     * )
     * @Theme("admin")
     */
    public function adminEdit(
        Request $request,
        PermissionHelper $permissionHelper,
        ControllerHelper $controllerHelper,
        ViewHelper $viewHelper,
        EditHandler $formHandler
    ): Response {
        return $this->editInternal(
            $request,
            $permissionHelper,
            $controllerHelper,
            $viewHelper,
            $formHandler,
            true
        );
    }
    
    /**
     * @Route("/entry/edit/{id}.{_format}",
     *        requirements = {"id" = "\d+", "_format" = "html"},
     *        defaults = {"id" = "0", "_format" = "html"},
     *        methods = {"GET", "POST"}
     * )
     */
    public function edit(
        Request $request,
        PermissionHelper $permissionHelper,
        ControllerHelper $controllerHelper,
        ViewHelper $viewHelper,
        EditHandler $formHandler
    ): Response {
        return $this->editInternal(
            $request,
            $permissionHelper,
            $controllerHelper,
            $viewHelper,
            $formHandler,
            false
        );
    }

    
    /**
     * Process status changes for multiple items.
     *
     * @Route("/admin/entries/handleSelectedEntries",
     *        methods = {"POST"}
     * )
     * @Theme("admin")
     */
    public function adminHandleSelectedEntries(
        Request $request,
        LoggerInterface $logger,
        EntityFactory $entityFactory,
        WorkflowHelper $workflowHelper,
        HookHelper $hookHelper,
        CurrentUserApiInterface $currentUserApi
    ): RedirectResponse {
        return $this->handleSelectedEntriesInternal(
            $request,
            $logger,
            $entityFactory,
            $workflowHelper,
            $hookHelper,
            $currentUserApi,
            true
        );
    }
    
    /**
     * Process status changes for multiple items.
     *
     * @Route("/entries/handleSelectedEntries",
     *        methods = {"POST"}
     * )
     */
    public function handleSelectedEntries(
        Request $request,
        LoggerInterface $logger,
        EntityFactory $entityFactory,
        WorkflowHelper $workflowHelper,
        HookHelper $hookHelper,
        CurrentUserApiInterface $currentUserApi
    ): RedirectResponse {
        return $this->handleSelectedEntriesInternal(
            $request,
            $logger,
            $entityFactory,
            $workflowHelper,
            $hookHelper,
            $currentUserApi,
            false
        );
    }

    // feel free to add your own controller methods here
}

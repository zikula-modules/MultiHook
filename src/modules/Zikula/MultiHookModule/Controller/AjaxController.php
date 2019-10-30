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

namespace Zikula\MultiHookModule\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Zikula\UsersModule\Api\ApiInterface\CurrentUserApiInterface;
use Zikula\MultiHookModule\Controller\Base\AbstractAjaxController;
use Zikula\MultiHookModule\Entity\Factory\EntityFactory;

/**
 * Ajax controller implementation class.
 *
 * @Route("/ajax")
 */
class AjaxController extends AbstractAjaxController
{
    
    /**
     * @Route("/toggleFlag", methods = {"POST"}, options={"expose"=true})
     */
    public function toggleFlagAction(
        Request $request,
        EntityFactory $entityFactory,
        CurrentUserApiInterface $currentUserApi
    ): JsonResponse
     {
        return parent::toggleFlagAction($request, $entityFactory, $currentUserApi);
    }

    // feel free to add your own ajax controller methods here
}

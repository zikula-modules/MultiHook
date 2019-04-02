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

namespace Zikula\MultiHookModule\Helper\Base;

use Exception;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\Common\Translator\TranslatorTrait;
use Zikula\Component\SortableColumns\SortableColumns;
use Zikula\Core\RouteUrl;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\MultiHookModule\Entity\Factory\EntityFactory;
use Zikula\MultiHookModule\Helper\CollectionFilterHelper;
use Zikula\MultiHookModule\Helper\FeatureActivationHelper;
use Zikula\MultiHookModule\Helper\ModelHelper;
use Zikula\MultiHookModule\Helper\PermissionHelper;

/**
 * Helper base class for controller layer methods.
 */
abstract class AbstractControllerHelper
{
    use TranslatorTrait;
    
    /**
     * @var RequestStack
     */
    protected $requestStack;
    
    /**
     * @var FormFactoryInterface
     */
    protected $formFactory;
    
    /**
     * @var VariableApiInterface
     */
    protected $variableApi;
    
    /**
     * @var EntityFactory
     */
    protected $entityFactory;
    
    /**
     * @var CollectionFilterHelper
     */
    protected $collectionFilterHelper;
    
    /**
     * @var PermissionHelper
     */
    protected $permissionHelper;
    
    /**
     * @var ModelHelper
     */
    protected $modelHelper;
    
    /**
     * @var FeatureActivationHelper
     */
    protected $featureActivationHelper;
    
    public function __construct(
        TranslatorInterface $translator,
        RequestStack $requestStack,
        FormFactoryInterface $formFactory,
        VariableApiInterface $variableApi,
        EntityFactory $entityFactory,
        CollectionFilterHelper $collectionFilterHelper,
        PermissionHelper $permissionHelper,
        ModelHelper $modelHelper,
        FeatureActivationHelper $featureActivationHelper
    ) {
        $this->setTranslator($translator);
        $this->requestStack = $requestStack;
        $this->formFactory = $formFactory;
        $this->variableApi = $variableApi;
        $this->entityFactory = $entityFactory;
        $this->collectionFilterHelper = $collectionFilterHelper;
        $this->permissionHelper = $permissionHelper;
        $this->modelHelper = $modelHelper;
        $this->featureActivationHelper = $featureActivationHelper;
    }
    
    public function setTranslator(TranslatorInterface $translator)
    {
        $this->translator = $translator;
    }
    
    /**
     * Returns an array of all allowed object types in ZikulaMultiHookModule.
     *
     * @param string $context Usage context (allowed values: controllerAction, api, helper, actionHandler, block, contentType, util)
     * @param array $args Additional arguments
     *
     * @return string[] List of allowed object types
     */
    public function getObjectTypes($context = '', array $args = [])
    {
        if (!in_array($context, ['controllerAction', 'api', 'helper', 'actionHandler', 'block', 'contentType', 'util'], true)) {
            $context = 'controllerAction';
        }
    
        $allowedObjectTypes = [];
        $allowedObjectTypes[] = 'entry';
    
        return $allowedObjectTypes;
    }
    
    /**
     * Returns the default object type in ZikulaMultiHookModule.
     *
     * @param string $context Usage context (allowed values: controllerAction, api, helper, actionHandler, block, contentType, util)
     * @param array $args Additional arguments
     *
     * @return string The name of the default object type
     */
    public function getDefaultObjectType($context = '', array $args = [])
    {
        if (!in_array($context, ['controllerAction', 'api', 'helper', 'actionHandler', 'block', 'contentType', 'util'], true)) {
            $context = 'controllerAction';
        }
    
        return 'entry';
    }
    
    /**
     * Processes the parameters for a view action.
     * This includes handling pagination, quick navigation forms and other aspects.
     *
     * @param string $objectType Name of treated entity type
     * @param SortableColumns $sortableColumns Used SortableColumns instance
     * @param array $templateParameters Template data
     * @param bool $hasHookSubscriber Whether hook subscribers are supported or not
     *
     * @return array Enriched template parameters used for creating the response
     */
    public function processViewActionParameters(
        $objectType,
        SortableColumns $sortableColumns,
        array $templateParameters = [],
        $hasHookSubscriber = false
    ) {
        $contextArgs = ['controller' => $objectType, 'action' => 'view'];
        if (!in_array($objectType, $this->getObjectTypes('controllerAction', $contextArgs), true)) {
            throw new Exception($this->__('Error! Invalid object type received.'));
        }
    
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            throw new Exception($this->__('Error! Controller helper needs a request.'));
        }
        $repository = $this->entityFactory->getRepository($objectType);
    
        // parameter for used sorting field
        list ($sort, $sortdir) = $this->determineDefaultViewSorting($objectType);
        $templateParameters['sort'] = $sort;
        $templateParameters['sortdir'] = strtolower($sortdir);
    
        $templateParameters['all'] = 'csv' === $request->getRequestFormat() ? 1 : $request->query->getInt('all');
        $templateParameters['own'] = (bool)$request->query->getInt('own', $this->variableApi->get('ZikulaMultiHookModule', 'showOnlyOwnEntries')) ? 1 : 0;
    
        $resultsPerPage = 0;
        if (1 !== $templateParameters['all']) {
            // the number of items displayed on a page for pagination
            $resultsPerPage = $request->query->getInt('num');
            if (in_array($resultsPerPage, [0, 10], true)) {
                $resultsPerPage = $this->variableApi->get('ZikulaMultiHookModule', $objectType . 'EntriesPerPage', 10);
            }
        }
        $templateParameters['num'] = $resultsPerPage;
        $templateParameters['tpl'] = $request->query->getAlnum('tpl');
    
        $templateParameters = $this->addTemplateParameters($objectType, $templateParameters, 'controllerAction', $contextArgs);
    
        $quickNavForm = $this->formFactory->create('Zikula\MultiHookModule\Form\Type\QuickNavigation\\' . ucfirst($objectType) . 'QuickNavType', $templateParameters);
        $quickNavForm->handleRequest($request);
        if ($quickNavForm->isSubmitted()) {
            $quickNavData = $quickNavForm->getData();
            foreach ($quickNavData as $fieldName => $fieldValue) {
                if ('routeArea' === $fieldName) {
                    continue;
                }
                if (in_array($fieldName, ['all', 'own', 'num'], true)) {
                    $templateParameters[$fieldName] = $fieldValue;
                } elseif ('sort' === $fieldName && !empty($fieldValue)) {
                    $sort = $fieldValue;
                } elseif ('sortdir' === $fieldName && !empty($fieldValue)) {
                    $sortdir = $fieldValue;
                } elseif (false === stripos($fieldName, 'thumbRuntimeOptions') && false === stripos($fieldName, 'featureActivationHelper') && false === stripos($fieldName, 'permissionHelper')) {
                    // set filter as query argument, fetched inside repository
                    $request->query->set($fieldName, $fieldValue);
                }
            }
        }
        $sortableColumns->setOrderBy($sortableColumns->getColumn($sort), strtoupper($sortdir));
        $resultsPerPage = $templateParameters['num'];
        $request->query->set('own', $templateParameters['own']);
    
        $urlParameters = $templateParameters;
        foreach ($urlParameters as $parameterName => $parameterValue) {
            if (false === stripos($parameterName, 'thumbRuntimeOptions')
                && false === stripos($parameterName, 'featureActivationHelper')
            ) {
                continue;
            }
            unset($urlParameters[$parameterName]);
        }
    
        $sortableColumns->setAdditionalUrlParameters($urlParameters);
    
        $where = '';
        if (1 === $templateParameters['all']) {
            // retrieve item list without pagination
            $entities = $repository->selectWhere($where, $sort . ' ' . $sortdir);
        } else {
            // the current offset which is used to calculate the pagination
            $currentPage = $request->query->getInt('pos', 1);
    
            // retrieve item list with pagination
            list($entities, $objectCount) = $repository->selectWherePaginated($where, $sort . ' ' . $sortdir, $currentPage, $resultsPerPage);
    
            $templateParameters['currentPage'] = $currentPage;
            $templateParameters['pager'] = [
                'amountOfItems' => $objectCount,
                'itemsPerPage' => $resultsPerPage
            ];
        }
    
        $templateParameters['sort'] = $sort;
        $templateParameters['sortdir'] = $sortdir;
        $templateParameters['items'] = $entities;
    
        if (true === $hasHookSubscriber) {
            // build RouteUrl instance for display hooks
            $urlParameters['_locale'] = $request->getLocale();
            $templateParameters['currentUrlObject'] = new RouteUrl('zikulamultihookmodule_' . strtolower($objectType) . '_view', $urlParameters);
        }
    
        $templateParameters['sort'] = $sortableColumns->generateSortableColumns();
        $templateParameters['quickNavForm'] = $quickNavForm->createView();
    
        $templateParameters['canBeCreated'] = $this->modelHelper->canBeCreated($objectType);
    
        $request->query->set('sort', $sort);
        $request->query->set('sortdir', $sortdir);
        // set current sorting in route parameters (e.g. for the pager)
        $routeParams = $request->attributes->get('_route_params');
        $routeParams['sort'] = $sort;
        $routeParams['sortdir'] = $sortdir;
        $request->attributes->set('_route_params', $routeParams);
    
        return $templateParameters;
    }
    
    /**
     * Determines the default sorting criteria.
     *
     * @param string $objectType Name of treated entity type
     *
     * @return array with sort field and sort direction
     */
    protected function determineDefaultViewSorting($objectType)
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return ['', 'ASC'];
        }
        $repository = $this->entityFactory->getRepository($objectType);
    
        $sort = $request->query->get('sort', '');
        if (empty($sort) || !in_array($sort, $repository->getAllowedSortingFields(), true)) {
            $sort = $repository->getDefaultSortingField();
            $request->query->set('sort', $sort);
            // set default sorting in route parameters (e.g. for the pager)
            $routeParams = $request->attributes->get('_route_params');
            $routeParams['sort'] = $sort;
            $request->attributes->set('_route_params', $routeParams);
        }
        $sortdir = $request->query->get('sortdir', 'ASC');
        if (false !== strpos($sort, ' DESC')) {
            $sort = str_replace(' DESC', '', $sort);
            $sortdir = 'desc';
        }
    
        return [$sort, $sortdir];
    }
    
    /**
     * Processes the parameters for an edit action.
     *
     * @param string $objectType Name of treated entity type
     * @param array $templateParameters Template data
     *
     * @return array Enriched template parameters used for creating the response
     */
    public function processEditActionParameters($objectType, array $templateParameters = [])
    {
        $contextArgs = ['controller' => $objectType, 'action' => 'edit'];
        if (!in_array($objectType, $this->getObjectTypes('controllerAction', $contextArgs), true)) {
            throw new Exception($this->__('Error! Invalid object type received.'));
        }
    
        return $this->addTemplateParameters($objectType, $templateParameters, 'controllerAction', $contextArgs);
    }
    
    /**
     * Returns an array of additional template variables which are specific to the object type.
     *
     * @param string $objectType Name of treated entity type
     * @param array $parameters Given parameters to enrich
     * @param string $context Usage context (allowed values: controllerAction, api, actionHandler, block, contentType)
     * @param array $args Additional arguments
     *
     * @return array List of template variables to be assigned
     */
    public function addTemplateParameters($objectType = '', array $parameters = [], $context = '', array $args = [])
    {
        if (!in_array($context, ['controllerAction', 'api', 'actionHandler', 'block', 'contentType', 'mailz'], true)) {
            $context = 'controllerAction';
        }
    
        if ('controllerAction' === $context) {
            if (!isset($args['action'])) {
                $routeName = $this->requestStack->getCurrentRequest()->get('_route');
                $routeNameParts = explode('_', $routeName);
                $args['action'] = end($routeNameParts);
            }
            if (in_array($args['action'], ['index', 'view'])) {
                $parameters = array_merge($parameters, $this->collectionFilterHelper->getViewQuickNavParameters($objectType, $context, $args));
            }
        }
        $parameters['permissionHelper'] = $this->permissionHelper;
    
        $parameters['featureActivationHelper'] = $this->featureActivationHelper;
    
        return $parameters;
    }
}

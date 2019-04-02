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

use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\RequestStack;
use Zikula\ExtensionsModule\Api\ApiInterface\VariableApiInterface;
use Zikula\UsersModule\Api\ApiInterface\CurrentUserApiInterface;
use Zikula\UsersModule\Constant as UsersConstant;
use Zikula\MultiHookModule\Helper\PermissionHelper;

/**
 * Entity collection filter helper base class.
 */
abstract class AbstractCollectionFilterHelper
{
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
    
    /**
     * @var bool Fallback value to determine whether only own entries should be selected or not
     */
    protected $showOnlyOwnEntries = false;
    
    public function __construct(
        RequestStack $requestStack,
        PermissionHelper $permissionHelper,
        CurrentUserApiInterface $currentUserApi,
        VariableApiInterface $variableApi
    ) {
        $this->requestStack = $requestStack;
        $this->permissionHelper = $permissionHelper;
        $this->currentUserApi = $currentUserApi;
        $this->showOnlyOwnEntries = (bool)$variableApi->get('ZikulaMultiHookModule', 'showOnlyOwnEntries');
    }
    
    /**
     * Returns an array of additional template variables for view quick navigation forms.
     *
     * @param string $objectType Name of treated entity type
     * @param string $context Usage context (allowed values: controllerAction, api, actionHandler, block, contentType)
     * @param array $args Additional arguments
     *
     * @return array List of template variables to be assigned
     */
    public function getViewQuickNavParameters($objectType = '', $context = '', array $args = [])
    {
        if (!in_array($context, ['controllerAction', 'api', 'actionHandler', 'block', 'contentType'], true)) {
            $context = 'controllerAction';
        }
    
        if ('entry' === $objectType) {
            return $this->getViewQuickNavParametersForEntry($context, $args);
        }
    
        return [];
    }
    
    /**
     * Adds quick navigation related filter options as where clauses.
     *
     * @param string $objectType Name of treated entity type
     * @param QueryBuilder $qb Query builder to be enhanced
     *
     * @return QueryBuilder Enriched query builder instance
     */
    public function addCommonViewFilters($objectType, QueryBuilder $qb)
    {
        if ('entry' === $objectType) {
            return $this->addCommonViewFiltersForEntry($qb);
        }
    
        return $qb;
    }
    
    /**
     * Adds default filters as where clauses.
     *
     * @param string $objectType Name of treated entity type
     * @param QueryBuilder $qb Query builder to be enhanced
     * @param array $parameters List of determined filter options
     *
     * @return QueryBuilder Enriched query builder instance
     */
    public function applyDefaultFilters($objectType, QueryBuilder $qb, array $parameters = [])
    {
        if ('entry' === $objectType) {
            return $this->applyDefaultFiltersForEntry($qb, $parameters);
        }
    
        return $qb;
    }
    
    /**
     * Returns an array of additional template variables for view quick navigation forms.
     *
     * @param string $context Usage context (allowed values: controllerAction, api, actionHandler, block, contentType)
     * @param array $args Additional arguments
     *
     * @return array List of template variables to be assigned
     */
    protected function getViewQuickNavParametersForEntry($context = '', array $args = [])
    {
        $parameters = [];
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $parameters;
        }
    
        $parameters['workflowState'] = $request->query->get('workflowState', '');
        $parameters['entryType'] = $request->query->get('entryType', '');
        $parameters['q'] = $request->query->get('q', '');
        $parameters['active'] = $request->query->get('active', '');
    
        return $parameters;
    }
    
    /**
     * Adds quick navigation related filter options as where clauses.
     *
     * @param QueryBuilder $qb Query builder to be enhanced
     *
     * @return QueryBuilder Enriched query builder instance
     */
    protected function addCommonViewFiltersForEntry(QueryBuilder $qb)
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $qb;
        }
        $routeName = $request->get('_route', '');
        if (false !== strpos($routeName, 'edit')) {
            return $qb;
        }
    
        $parameters = $this->getViewQuickNavParametersForEntry();
        foreach ($parameters as $k => $v) {
            if (null === $v) {
                continue;
            }
            if (in_array($k, ['q', 'searchterm'], true)) {
                // quick search
                if (!empty($v)) {
                    $qb = $this->addSearchFilter('entry', $qb, $v);
                }
                continue;
            }
            if (in_array($k, ['active'], true)) {
                // boolean filter
                if ('no' === $v) {
                    $qb->andWhere('tbl.' . $k . ' = 0');
                } elseif ('yes' === $v || '1' === $v) {
                    $qb->andWhere('tbl.' . $k . ' = 1');
                }
                continue;
            }
    
            if (is_array($v)) {
                continue;
            }
    
            // field filter
            if ((!is_numeric($v) && '' !== $v) || (is_numeric($v) && 0 < $v)) {
                if ('workflowState' === $k && '0' === strpos($v, '!')) {
                    $qb->andWhere('tbl.' . $k . ' != :' . $k)
                       ->setParameter($k, substr($v, 1));
                } elseif (0 === strpos($v, '%')) {
                    $qb->andWhere('tbl.' . $k . ' LIKE :' . $k)
                       ->setParameter($k, '%' . substr($v, 1) . '%');
                } else {
                    $qb->andWhere('tbl.' . $k . ' = :' . $k)
                       ->setParameter($k, $v);
                }
            }
        }
    
        return $this->applyDefaultFiltersForEntry($qb, $parameters);
    }
    
    /**
     * Adds default filters as where clauses.
     *
     * @param QueryBuilder $qb Query builder to be enhanced
     * @param array $parameters List of determined filter options
     *
     * @return QueryBuilder Enriched query builder instance
     */
    protected function applyDefaultFiltersForEntry(QueryBuilder $qb, array $parameters = [])
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return $qb;
        }
        $routeName = $request->get('_route', '');
        $isAdminArea = false !== strpos($routeName, 'zikulamultihookmodule_entry_admin');
        if ($isAdminArea) {
            return $qb;
        }
    
        $showOnlyOwnEntries = (bool)$request->query->getInt('own', $this->showOnlyOwnEntries);
    
        if (!array_key_exists('workflowState', $parameters) || empty($parameters['workflowState'])) {
            // per default we show approved entries only
            $onlineStates = ['approved'];
            $qb->andWhere('tbl.workflowState IN (:onlineStates)')
               ->setParameter('onlineStates', $onlineStates);
        }
    
        if ($showOnlyOwnEntries) {
            $qb = $this->addCreatorFilter($qb);
        }
    
        return $qb;
    }
    
    /**
     * Adds a where clause for search query.
     *
     * @param string $objectType Name of treated entity type
     * @param QueryBuilder $qb Query builder to be enhanced
     * @param string $fragment The fragment to search for
     *
     * @return QueryBuilder Enriched query builder instance
     */
    public function addSearchFilter($objectType, QueryBuilder $qb, $fragment = '')
    {
        if ('' === $fragment) {
            return $qb;
        }
    
        $filters = [];
        $parameters = [];
    
        if ('entry' === $objectType) {
            $filters[] = 'tbl.shortForm LIKE :searchShortForm';
            $parameters['searchShortForm'] = '%' . $fragment . '%';
            $filters[] = 'tbl.longForm LIKE :searchLongForm';
            $parameters['searchLongForm'] = '%' . $fragment . '%';
            $filters[] = 'tbl.title LIKE :searchTitle';
            $parameters['searchTitle'] = '%' . $fragment . '%';
            $filters[] = 'tbl.entryType = :searchEntryType';
            $parameters['searchEntryType'] = $fragment;
        }
    
        $qb->andWhere('(' . implode(' OR ', $filters) . ')');
    
        foreach ($parameters as $parameterName => $parameterValue) {
            $qb->setParameter($parameterName, $parameterValue);
        }
    
        return $qb;
    }
    
    /**
     * Adds a filter for the createdBy field.
     *
     * @param QueryBuilder $qb Query builder to be enhanced
     * @param int $userId The user identifier used for filtering
     *
     * @return QueryBuilder Enriched query builder instance
     */
    public function addCreatorFilter(QueryBuilder $qb, $userId = null)
    {
        if (null === $userId) {
            $userId = $this->currentUserApi->isLoggedIn() ? (int)$this->currentUserApi->get('uid') : UsersConstant::USER_ID_ANONYMOUS;
        }
    
        $qb->andWhere('tbl.createdBy = :userId')
           ->setParameter('userId', $userId);
    
        return $qb;
    }
}

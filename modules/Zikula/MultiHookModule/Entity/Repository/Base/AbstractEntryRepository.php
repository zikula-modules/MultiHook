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

namespace Zikula\MultiHookModule\Entity\Repository\Base;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityRepository;

use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Gedmo\Translatable\Query\TreeWalker\TranslationWalker;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Zikula\Common\Translator\TranslatorInterface;
use Zikula\UsersModule\Api\ApiInterface\CurrentUserApiInterface;
use Zikula\MultiHookModule\Entity\EntryEntity;
use Zikula\MultiHookModule\Helper\CollectionFilterHelper;

/**
 * Repository class used to implement own convenience methods for performing certain DQL queries.
 *
 * This is the base repository class for entry entities.
 */
abstract class AbstractEntryRepository extends EntityRepository
{
    /**
     * @var string The main entity class
     */
    protected $mainEntityClass = EntryEntity::class;

    /**
     * @var string The default sorting field/expression
     */
    protected $defaultSortingField = 'shortForm';

    /**
     * @var CollectionFilterHelper
     */
    protected $collectionFilterHelper;

    /**
     * @var bool Whether translations are enabled or not
     */
    protected $translationsEnabled = true;

    /**
     * Retrieves an array with all fields which can be used for sorting instances.
     *
     * @return string[] List of sorting field names
     */
    public function getAllowedSortingFields(): array
    {
        return [
            'shortForm',
            'longForm',
            'title',
            'entryType',
            'active',
            'createdBy',
            'createdDate',
            'updatedBy',
            'updatedDate',
        ];
    }

    public function getDefaultSortingField(): ?string
    {
        return $this->defaultSortingField;
    }
    
    public function setDefaultSortingField(string $defaultSortingField = null): void
    {
        if ($this->defaultSortingField !== $defaultSortingField) {
            $this->defaultSortingField = $defaultSortingField;
        }
    }
    
    public function getCollectionFilterHelper(): ?CollectionFilterHelper
    {
        return $this->collectionFilterHelper;
    }
    
    public function setCollectionFilterHelper(CollectionFilterHelper $collectionFilterHelper = null): void
    {
        if ($this->collectionFilterHelper !== $collectionFilterHelper) {
            $this->collectionFilterHelper = $collectionFilterHelper;
        }
    }
    
    public function getTranslationsEnabled(): ?bool
    {
        return $this->translationsEnabled;
    }
    
    public function setTranslationsEnabled(bool $translationsEnabled = null): void
    {
        if ($this->translationsEnabled !== $translationsEnabled) {
            $this->translationsEnabled = $translationsEnabled;
        }
    }
    

    /**
     * Updates the creator of all objects created by a certain user.
     *
     * @throws InvalidArgumentException Thrown if invalid parameters are received
     */
    public function updateCreator(
        int $userId,
        int $newUserId,
        TranslatorInterface $translator,
        LoggerInterface $logger,
        CurrentUserApiInterface $currentUserApi
    ): void {
        if (0 === $userId || 0 === $newUserId) {
            throw new InvalidArgumentException($translator->__('Invalid user identifier received.'));
        }
    
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->update($this->mainEntityClass, 'tbl')
           ->set('tbl.createdBy', $newUserId)
           ->where('tbl.createdBy = :creator')
           ->setParameter('creator', $userId);
        $query = $qb->getQuery();
        $query->execute();
    
        $logArgs = ['app' => 'ZikulaMultiHookModule', 'user' => $currentUserApi->get('uname'), 'entities' => 'entries', 'userid' => $userId];
        $logger->debug('{app}: User {user} updated {entities} created by user id {userid}.', $logArgs);
    }
    
    /**
     * Updates the last editor of all objects updated by a certain user.
     *
     * @throws InvalidArgumentException Thrown if invalid parameters are received
     */
    public function updateLastEditor(
        int $userId,
        int $newUserId,
        TranslatorInterface $translator,
        LoggerInterface $logger,
        CurrentUserApiInterface $currentUserApi
    ): void {
        if (0 === $userId || 0 === $newUserId) {
            throw new InvalidArgumentException($translator->__('Invalid user identifier received.'));
        }
    
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->update($this->mainEntityClass, 'tbl')
           ->set('tbl.updatedBy', $newUserId)
           ->where('tbl.updatedBy = :editor')
           ->setParameter('editor', $userId);
        $query = $qb->getQuery();
        $query->execute();
    
        $logArgs = ['app' => 'ZikulaMultiHookModule', 'user' => $currentUserApi->get('uname'), 'entities' => 'entries', 'userid' => $userId];
        $logger->debug('{app}: User {user} updated {entities} edited by user id {userid}.', $logArgs);
    }
    
    /**
     * Deletes all objects created by a certain user.
     *
     * @throws InvalidArgumentException Thrown if invalid parameters are received
     */
    public function deleteByCreator(
        int $userId,
        TranslatorInterface $translator,
        LoggerInterface $logger,
        CurrentUserApiInterface $currentUserApi
    ): void {
        if (0 === $userId) {
            throw new InvalidArgumentException($translator->__('Invalid user identifier received.'));
        }
    
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->delete($this->mainEntityClass, 'tbl')
           ->where('tbl.createdBy = :creator')
           ->setParameter('creator', $userId);
        $query = $qb->getQuery();
        $query->execute();
    
        $logArgs = ['app' => 'ZikulaMultiHookModule', 'user' => $currentUserApi->get('uname'), 'entities' => 'entries', 'userid' => $userId];
        $logger->debug('{app}: User {user} deleted {entities} created by user id {userid}.', $logArgs);
    }
    
    /**
     * Deletes all objects updated by a certain user.
     *
     * @throws InvalidArgumentException Thrown if invalid parameters are received
     */
    public function deleteByLastEditor(
        int $userId,
        TranslatorInterface $translator,
        LoggerInterface $logger,
        CurrentUserApiInterface $currentUserApi
    ): void {
        if (0 === $userId) {
            throw new InvalidArgumentException($translator->__('Invalid user identifier received.'));
        }
    
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->delete($this->mainEntityClass, 'tbl')
           ->where('tbl.updatedBy = :editor')
           ->setParameter('editor', $userId);
        $query = $qb->getQuery();
        $query->execute();
    
        $logArgs = ['app' => 'ZikulaMultiHookModule', 'user' => $currentUserApi->get('uname'), 'entities' => 'entries', 'userid' => $userId];
        $logger->debug('{app}: User {user} deleted {entities} edited by user id {userid}.', $logArgs);
    }

    /**
     * Adds an array of id filters to given query instance.
     *
     * @throws InvalidArgumentException Thrown if invalid parameters are received
     */
    protected function addIdListFilter(array $idList, QueryBuilder $qb): QueryBuilder
    {
        $orX = $qb->expr()->orX();
    
        foreach ($idList as $id) {
            if (0 === $id) {
                throw new InvalidArgumentException('Invalid identifier received.');
            }
    
            $orX->add($qb->expr()->eq('tbl.id', $id));
        }
    
        $qb->andWhere($orX);
    
        return $qb;
    }
    
    /**
     * Selects an object from the database.
     *
     * @param mixed $id The id (or array of ids) to use to retrieve the object (optional) (default=0)
     * @param bool $useJoins Whether to include joining related objects (optional) (default=true)
     * @param bool $slimMode If activated only some basic fields are selected without using any joins (optional) (default=false)
     *
     * @return array|EntryEntity Retrieved data array or entryEntity instance
     */
    public function selectById($id = 0, bool $useJoins = true, bool $slimMode = false)
    {
        $results = $this->selectByIdList(is_array($id) ? $id : [$id], $useJoins, $slimMode);
    
        return null !== $results && 0 < count($results) ? $results[0] : null;
    }
    
    /**
     * Selects a list of objects with an array of ids
     *
     * @param array $idList The array of ids to use to retrieve the objects (optional) (default=0)
     * @param bool $useJoins Whether to include joining related objects (optional) (default=true)
     * @param bool $slimMode If activated only some basic fields are selected without using any joins (optional) (default=false)
     *
     * @return array Retrieved EntryEntity instances
     */
    public function selectByIdList(array $idList = [0], bool $useJoins = true, bool $slimMode = false): ?array
    {
        $qb = $this->genericBaseQuery('', '', $useJoins, $slimMode);
        $qb = $this->addIdListFilter($idList, $qb);
    
        if (!$slimMode && null !== $this->collectionFilterHelper) {
            $qb = $this->collectionFilterHelper->applyDefaultFilters('entry', $qb);
        }
    
        $query = $this->getQueryFromBuilder($qb);
    
        $results = $query->getResult();
    
        return 0 < count($results) ? $results : null;
    }

    /**
     * Adds where clauses excluding desired identifiers from selection.
     */
    protected function addExclusion(QueryBuilder $qb, array $exclusions = []): QueryBuilder
    {
        if (0 < count($exclusions)) {
            $qb->andWhere('tbl.id NOT IN (:excludedIdentifiers)')
               ->setParameter('excludedIdentifiers', $exclusions);
        }
    
        return $qb;
    }

    /**
     * Returns query builder for selecting a list of objects with a given where clause.
     */
    public function getListQueryBuilder(string $where = '', string $orderBy = '', bool $useJoins = true, bool $slimMode = false): QueryBuilder
    {
        $qb = $this->genericBaseQuery($where, $orderBy, $useJoins, $slimMode);
        if (!$slimMode && null !== $this->collectionFilterHelper) {
            $qb = $this->collectionFilterHelper->addCommonViewFilters('entry', $qb);
        }
    
        return $qb;
    }
    
    /**
     * Selects a list of objects with a given where clause.
     */
    public function selectWhere(string $where = '', string $orderBy = '', bool $useJoins = true, bool $slimMode = false): array
    {
        $qb = $this->getListQueryBuilder($where, $orderBy, $useJoins, $slimMode);
    
        $query = $this->getQueryFromBuilder($qb);
    
        return $this->retrieveCollectionResult($query);
    }

    /**
     * Returns query builder instance for retrieving a list of objects with a given where clause and pagination parameters.
     */
    public function getSelectWherePaginatedQuery(QueryBuilder $qb, int $currentPage = 1, int $resultsPerPage = 25): Query
    {
        if (1 > $currentPage) {
            $currentPage = 1;
        }
        if (1 > $resultsPerPage) {
            $resultsPerPage = 25;
        }
        $query = $this->getQueryFromBuilder($qb);
        $offset = ($currentPage - 1) * $resultsPerPage;
    
        $query->setFirstResult($offset)
              ->setMaxResults($resultsPerPage);
    
        return $query;
    }
    
    /**
     * Selects a list of objects with a given where clause and pagination parameters.
     *
     * @return array Retrieved collection and the amount of total records affected
     */
    public function selectWherePaginated(string $where = '', string $orderBy = '', int $currentPage = 1, int $resultsPerPage = 25, bool $useJoins = true, bool $slimMode = false): array
    {
        $qb = $this->getListQueryBuilder($where, $orderBy, $useJoins, $slimMode);
        $query = $this->getSelectWherePaginatedQuery($qb, $currentPage, $resultsPerPage);
    
        return $this->retrieveCollectionResult($query, true);
    }

    /**
     * Selects entities by a given search fragment.
     *
     * @return array Retrieved collection and (for paginated queries) the amount of total records affected
     */
    public function selectSearch(string $fragment = '', array $exclude = [], string $orderBy = '', int $currentPage = 1, int $resultsPerPage = 25, bool $useJoins = true): array
    {
        $qb = $this->getListQueryBuilder('', $orderBy, $useJoins);
        if (0 < count($exclude)) {
            $qb = $this->addExclusion($qb, $exclude);
        }
    
        if (null !== $this->collectionFilterHelper) {
            $qb = $this->collectionFilterHelper->addSearchFilter('entry', $qb, $fragment);
        }
    
        $query = $this->getSelectWherePaginatedQuery($qb, $currentPage, $resultsPerPage);
    
        return $this->retrieveCollectionResult($query, true);
    }

    /**
     * Performs a given database selection and post-processed the results.
     *
     * @return array Retrieved collection and (for paginated queries) the amount of total records affected
     */
    public function retrieveCollectionResult(Query $query, bool $isPaginated = false): array
    {
        $count = 0;
        if (!$isPaginated) {
            $result = $query->getResult();
        } else {
            $paginator = new Paginator($query, false);
            if (true === $this->translationsEnabled) {
                $paginator->setUseOutputWalkers(true);
            }
    
            $count = count($paginator);
            $result = $paginator;
        }
    
        if (!$isPaginated) {
            return $result;
        }
    
        return [$result, $count];
    }

    /**
     * Returns query builder instance for a count query.
     */
    public function getCountQuery(string $where = '', bool $useJoins = false): QueryBuilder
    {
        $selection = 'COUNT(tbl.id) AS numEntries';
    
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select($selection)
           ->from($this->mainEntityClass, 'tbl');
    
        if (true === $useJoins) {
            $this->addJoinsToFrom($qb);
        }
    
        if (!empty($where)) {
            $qb->andWhere($where);
        }
    
        return $qb;
    }

    /**
     * Selects entity count with a given where clause.
     */
    public function selectCount(string $where = '', bool $useJoins = false, array $parameters = []): int
    {
        $qb = $this->getCountQuery($where, $useJoins);
    
        if (null !== $this->collectionFilterHelper) {
            $qb = $this->collectionFilterHelper->applyDefaultFilters('entry', $qb, $parameters);
        }
    
        $query = $qb->getQuery();
    
        return (int)$query->getSingleScalarResult();
    }


    /**
     * Checks for unique values.
     */
    public function detectUniqueState(string $fieldName, string $fieldValue, int $excludeId = 0): bool
    {
        $qb = $this->getCountQuery();
        $qb->andWhere('tbl.' . $fieldName . ' = :' . $fieldName)
           ->setParameter($fieldName, $fieldValue);
    
        if ($excludeId > 0) {
            $qb = $this->addExclusion($qb, [$excludeId]);
        }
    
        $query = $qb->getQuery();
    
        $count = (int)$query->getSingleScalarResult();
    
        return 1 > $count;
    }

    /**
     * Builds a generic Doctrine query supporting WHERE and ORDER BY.
     */
    public function genericBaseQuery(string $where = '', string $orderBy = '', bool $useJoins = true, bool $slimMode = false): QueryBuilder
    {
        // normally we select the whole table
        $selection = 'tbl';
    
        if (true === $slimMode) {
            // but for the slim version we select only the basic fields, and no joins
    
            $selection = 'tbl.id';
            $selection .= ', tbl.shortForm';
            $useJoins = false;
        }
    
        if (true === $useJoins) {
            $selection .= $this->addJoinsToSelection();
        }
    
        $qb = $this->getEntityManager()->createQueryBuilder();
        $qb->select($selection)
           ->from($this->mainEntityClass, 'tbl');
    
        if (true === $useJoins) {
            $this->addJoinsToFrom($qb);
        }
    
        if (!empty($where)) {
            $qb->andWhere($where);
        }
    
        $this->genericBaseQueryAddOrderBy($qb, $orderBy);
    
        return $qb;
    }

    /**
     * Adds ORDER BY clause to given query builder.
     */
    protected function genericBaseQueryAddOrderBy(QueryBuilder $qb, string $orderBy = ''): QueryBuilder
    {
        if ('RAND()' === $orderBy) {
            // random selection
            $qb->addSelect('MOD(tbl.id, ' . random_int(2, 15) . ') AS HIDDEN randomIdentifiers')
               ->orderBy('randomIdentifiers');
    
            return $qb;
        }
    
        if (empty($orderBy)) {
            $orderBy = $this->defaultSortingField;
        }
    
        if (empty($orderBy)) {
            return $qb;
        }
    
        // add order by clause
        if (false === strpos($orderBy, '.')) {
            $orderBy = 'tbl.' . $orderBy;
        }
        if (false !== strpos($orderBy, 'tbl.createdBy')) {
            $qb->addSelect('tblCreator')
               ->leftJoin('tbl.createdBy', 'tblCreator');
            $orderBy = str_replace('tbl.createdBy', 'tblCreator.uname', $orderBy);
        }
        if (false !== strpos($orderBy, 'tbl.updatedBy')) {
            $qb->addSelect('tblUpdater')
               ->leftJoin('tbl.updatedBy', 'tblUpdater');
            $orderBy = str_replace('tbl.updatedBy', 'tblUpdater.uname', $orderBy);
        }
        $qb->add('orderBy', $orderBy);
    
        return $qb;
    }

    /**
     * Retrieves Doctrine query from query builder.
     */
    public function getQueryFromBuilder(QueryBuilder $qb): Query
    {
        $query = $qb->getQuery();
    
        if (true === $this->translationsEnabled) {
            // set the translation query hint
            $query->setHint(Query::HINT_CUSTOM_OUTPUT_WALKER, TranslationWalker::class);
        }
    
        return $query;
    }

    /**
     * Helper method to add join selections.
     */
    protected function addJoinsToSelection(): string
    {
        $selection = '';
    
        return $selection;
    }
    
    /**
     * Helper method to add joins to from clause.
     */
    protected function addJoinsToFrom(QueryBuilder $qb): QueryBuilder
    {
    
        return $qb;
    }
}

<?php

namespaceAngkosal\Repository\Repositories\Eloquent\Criteria;

use Angkosal\Repository\Repositories\Criteria\CriterionInterface;

class EagerLoad implements CriterionInterface
{
    /**
     * Relations to eagerload.
     *
     * @var array
     */
    protected $relations;

    /**
     * @param array $relations
     */
    public function __construct(array $relations)
    {
        $this->relations = $relations;
    }

    /**
     * Apply the query filtering.
     *
     * @param \Illuminate\Database\Eloquent\Builder $entity
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function apply($entity)
    {
        return $entity->with($this->relations);
    }
}

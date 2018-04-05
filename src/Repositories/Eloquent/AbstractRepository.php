<?php

namespace Angkosal\Repository\Repositories\Eloquent;

use Angkosal\Repository\Exceptions\NoModelDefined;
use Angkosal\Repository\Exceptions\RepositoryException;
use Angkosal\Repository\Repositories\Contracts\CriteriaInterface;
use Angkosal\Repository\Repositories\Contracts\RepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;

abstract class AbstractRepository implements RepositoryInterface, CriteriaInterface
{
    /**
     * @var mixed
     */
    protected $model;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->model = $this->resolveModel();
    }

    /**
     * @param null|mixed $paginate
     *
     * @return mixed
     */
    public function all($paginate = null)
    {
        return $this->processPagination($this->model, $paginate);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function find($id)
    {
        $model = $this->model->find($id);

        if (!$model) {
            throw (new ModelNotFoundException())->setModel(
                get_class($this->model->getModel()),
                $id
            );
        }

        return $model;
    }

    /**
     * @param $column
     * @param $value
     * @param null|mixed $paginate
     *
     * @return mixed
     */
    public function findWhere($column, $value, $paginate = null)
    {
        $query = $this->model->where($column, $value);

        return $this->processPagination($query, $paginate);
    }

    /**
     * @param $column
     * @param $value
     *
     * @return mixed
     */
    public function findWhereFirst($column, $value)
    {
        $model = $this->model->where($column, $value)->first();

        if (!$model) {
            throw (new ModelNotFoundException())->setModel(
                get_class($this->model->getModel())
            );
        }

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    public function findWhereLike($columns, $value, $paginate = null)
    {
        $query = $this->model;

        if (is_string($columns)) {
            $columns = [$columns];
        }

        foreach ($columns as $column) {
            $query->orWhere($column, 'like', $value);
        }

        return $this->processPagination($query, $paginate);
    }

    /**
     * @param $perPage
     *
     * @return mixed
     */
    public function paginate($perPage = 10)
    {
        return $this->model->paginate($perPage);
    }

    /**
     * @param array $properties
     *
     * @return mixed
     */
    public function create(array $properties)
    {
        return $this->model->create($properties);
    }

    /**
     * @param $id
     * @param array $properties
     *
     * @return mixed
     */
    public function update($id, array $properties)
    {
        return $this->find($id)->update($properties);
    }

    /**
     * @param $id
     *
     * @return mixed
     */
    public function delete($id)
    {
        return $this->find($id)->delete();
    }

    /**
     * @param $criteria
     *
     * @return mixed
     */
    public function withCriteria(...$criteria)
    {
        $criteria = array_flatten($criteria);

        foreach ($criteria as $criterion) {
            $this->model = $criterion->apply($this->model);
        }

        return $this;
    }

    /**
     * Resolve model.
     */
    protected function resolveModel()
    {
        if (!method_exists($this, 'model')) {
            throw new NoModelDefined('Method model not defined');
        }

        $model = app($this->model());

        if (!$model instanceof Model) {
            throw new RepositoryException("Class {$this->model()} must be an instance of Illuminate\\Database\\Eloquent\\Model");
        }

        return $model;
    }

    /**
     * Build pagination.
     *
     * @param Builder  $query
     * @param null|int $paginate
     *
     * @return Collection
     */
    private function processPagination($query, $paginate)
    {
        return $paginate ? $query->paginate($paginate) : $query->get();
    }
}

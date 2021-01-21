<?php namespace App\Repositories\Eloquent;

use App\Repositories\Contracts\RepositoryInterface;
use App\Repositories\Exceptions\RepositoryException;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Collection;

use Illuminate\Container\Container as App;

/**
 * Class Repository
 * @package App\Repositories\Eloquent
 */
abstract class Repository implements RepositoryInterface {

    /**      
     * @var Model      
     */     
    protected $model;       

    /**      
     * BaseRepository constructor.      
     *      
     * @param Model $model      
     */     
    public function __construct(Model $model)     
    {         
        $this->model = $model;
    }

    /**
     * @param array $columns
     * @return mixed
     */
    public function all($columns = array('*')) {
        return $this->model->get($columns);
    }

    /**
     * @param int $perPage
     * @param array $columns
     * @return mixed
     */
    public function paginate($perPage = 15, $columns = array('*')) {
        return $this->model->paginate($perPage, $columns);
    }

    /**
     * @param array $data
     * @return mixed
     */
    public function create(array $data) {
        return $this->model->create($data);
    }

    /**
     * @param array $data
     * @param $id
     * @param string $attribute
     * @return mixed
     */
    public function update(array $data, $id, $attribute="id") {
        return $this->model->where($attribute, '=', $id)->update($data);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function delete($id) {
        return $this->model->destroy($id);
    }

    /**
     * @param $id
     * @param array $columns
     * @return mixed
     */
    public function find($id, $columns = array('*')) {
        return $this->model->find($id, $columns);
    }

    /**
     * @param $attribute
     * @param $value
     * @param array $columns
     * @return mixed
     */
    public function findBy($attribute, $value, $columns = array('*')) {
        return $this->model->where($attribute, '=', $value)->first($columns);
    }

    public function findOneByPrimary(int $primary)
    {
        return $this->model->find($primary);
        // return $throwsExceptionIfNotFound
        //     ? $this->model->findOrFail($primary)
        //     : $this->model->find($primary);
    }

    public function findOneFromArray(array $data)
    {
        return $this->model->where($data)->first();
        // return $throwsExceptionIfNotFound
        //     ? $this->model->where($data)->firstOrFail()
        //     : $this->model->where($data)->first();
    }

    public function findMultipleFromArray(array $data): Collection
    {
        return $this->model->where($data)->get();
    }

    /**
     * Find multiple model instances from an array of ids.
     *
     * @param array $primaries
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function findMultipleFromPrimaries(array $primaries): Collection
    {
        return $this->getModel()->findMany($primaries);
    }

}
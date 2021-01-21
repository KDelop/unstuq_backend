<?php namespace App\Repositories\Contracts;

use Illuminate\Support\Collection;

interface RepositoryInterface {

    public function all($columns = array('*'));

    public function paginate($perPage = 15, $columns = array('*'));

    public function create(array $data);

    public function update(array $data, $id);

    public function delete($id);

    public function find($id, $columns = array('*'));

    public function findBy($field, $value, $columns = array('*'));

    public function findOneByPrimary(int $primary);

    public function findOneFromArray(array $data);

    public function findMultipleFromArray(array $data): Collection;
    
    public function findMultipleFromPrimaries(array $primaries): Collection;
}
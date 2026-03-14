<?php

namespace App\Filters\V1;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use function explode;
use function in_array;
use function method_exists;

abstract class QueryFilter
{
    protected Builder $builder;
    protected Request $request;
    protected array $allowerFilters = [];
    protected array $multiValueFilters = [];

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;

        foreach ($this->request->all() as $key => $value) {
            if (!in_array($key, $this->allowerFilters)) continue;

            if (method_exists($this, $key)) {
                if (in_array($key, $this->multiValueFilters)) {
                    $this->$key(explode(',', $value));
                } else {
                    $this->$key($value);
                }
            }
        }

        return $this->builder;
    }

    public function getPerPage(int $default = 15)
    {
        $perPage = (int)$this->request->query('perPage', $default);
        return min($perPage, 15);
    }
}

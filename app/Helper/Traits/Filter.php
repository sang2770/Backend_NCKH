<?php

namespace App\Helper\Traits;

use Illuminate\Support\Arr;
use Illuminate\Support\Str;

trait Filterable
{
    public function scopeFilter($query, $request)
    {
        $param = Arr::except($request->query(), ['page', 'limit']);
        foreach ($param as $field => $value) {
            if (strlen(trim($value)) > 0) {
                $method = 'filter' . Str::studly($field);
                if (method_exists($this, $method)) {
                    $this->{$method}($query, $value);
                }
            }
        }
        return $query;
    }
}

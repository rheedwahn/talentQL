<?php

namespace App\Services\Base;

use Illuminate\Support\Arr;

class UpdateService
{
    protected $model;
    protected $data;
    protected $update_fields;

    public function __construct($model, $data, $update_fields)
    {
        $this->model = $model;
        $this->data = $data;
        $this->update_fields = $update_fields;
    }

    public function run()
    {
        $this->updateModel($this->data, $this->model, $this->update_fields);
    }

    protected function updateModel($data, $model, $update_fields)
    {
        foreach ($update_fields as $key) {
            if (Arr::has($data, $key)) {
                $model->setAttribute($key, $data[$key]);
            }
        }
        $model->save();
    }
}

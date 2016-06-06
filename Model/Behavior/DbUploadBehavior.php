<?php

class DbUploadBehavior extends ModelBehavior
{

    public function setup(Model $model, $config = array())
    {
        parent::setup($model, $config);
        foreach ($config as $field) {
            $this->settings[$model->alias]['fields'][] = $field;
        }
        $this->validateDb($model);
    }

    private function validateDb(Model $model)
    {
        $columns = $model->getColumnTypes();
        foreach ($this->settings[$model->alias]['fields'] as $field) {
            if (!isset($columns["{$field}_name"])) {
                throw new FatalErrorException(__('The table %s for the model %s is missing the field %s!', $model->table, $model->alias, "{$field}_name"));
            }
            if (!isset($columns["{$field}_type"])) {
                throw new FatalErrorException(__('The table %s for the model %s is missing the field %s!', $model->table, $model->alias, "{$field}_type"));
            }
            if (!isset($columns["{$field}_size"])) {
                throw new FatalErrorException(__('The table %s for the model %s is missing the field %s!', $model->table, $model->alias, "{$field}_size"));
            }
            if (!isset($columns["{$field}_content"])) {
                throw new FatalErrorException(__('The table %s for the model %s is missing the field %s!', $model->table, $model->alias, "{$field}_content"));
            }
        }
    }

    public function beforeSave(Model $model, $options = array())
    {
        foreach ($this->settings[$model->alias]['fields'] as $field) {
            if (!empty($model->data[$model->alias][$field])) {
                $model->data[$model->alias]["{$field}_name"] = $model->data[$model->alias][$field]['name'];

                $model->data[$model->alias]["{$field}_type"] = finfo_file(finfo_open(FILEINFO_MIME_TYPE), $model->data[$model->alias][$field]['tmp_name']);
                $model->data[$model->alias]["{$field}_size"] = $model->data[$model->alias][$field]['size'];
                $model->data[$model->alias]["{$field}_content"] = base64_encode(file_get_contents($model->data[$model->alias][$field]['tmp_name']));
                unset($model->data[$model->alias][$field]);
            }
        }
        return parent::beforeSave($model, $options);
    }
}


<?php
/**
 * DbUpload: https://github.com/hevertonfreitas/DbUpload
 * Copyright (c) Heverton Coneglian de Freitas <hevertonconeglian@gmail.com>.
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Heverton Coneglian de Freitas <hevertonconeglian@gmail.com>
 * @link          https://github.com/hevertonfreitas/DbUpload
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/**
 * Class DbUploadBehavior
 *
 * @author Heverton Coneglian de Freitas <hevertonfreitas1@yahoo.com.br>
 */
class DbUploadBehavior extends ModelBehavior
{

    /**
     * Setup this behavior with the specified configuration settings.
     *
     * @param Model $model Model using this behavior
     * @param array $config Configuration settings for $model
     * @return void
     */
    public function setup(Model $model, $config = array())
    {
        parent::setup($model, $config);
        foreach ($config as $field) {
            $this->settings[$model->alias]['fields'][] = $field;
        }
        $this->validateDb($model);
    }

    /**
     * Validate the presence of the required fields in the database
     *
     * @param Model $model
     */
    private function validateDb(Model $model)
    {
        $columns = $model->getColumnTypes();
        foreach ($this->settings[$model->alias]['fields'] as $field) {
            if (!isset($columns[$field])) {
                throw new FatalErrorException(__('The table %s for the model %s is missing the field %s!', $model->table, $model->alias, $field));
            }
        }
    }

    /**
     * beforeSave is called before a model is saved. Returning false from a beforeSave callback
     * will abort the save operation.
     *
     * @param Model $model Model using this behavior
     * @param array $options Options passed from Model::save().
     * @return mixed False if the operation should abort. Any other result will continue.
     * @see Model::save()
     */
    public function beforeSave(Model $model, $options = array())
    {
        foreach ($this->settings[$model->alias]['fields'] as $field) {
            if (!empty($model->data[$model->alias][$field]) && file_exists($model->data[$model->alias][$field]['tmp_name'])) {
                $data = array(
                    'name' => $model->data[$model->alias][$field]['name'],
                    'type' => finfo_file(finfo_open(FILEINFO_MIME_TYPE), $model->data[$model->alias][$field]['tmp_name']),
                    'size' => $model->data[$model->alias][$field]['size'],
                    'content' => base64_encode(file_get_contents($model->data[$model->alias][$field]['tmp_name']))
                );

                $model->data[$model->alias][$field] = json_encode($data);
            } else {
                unset($model->data[$model->alias][$field]);
            }
        }
        return true;
    }
}


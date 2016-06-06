<?php
/**
 * DbUpload: https://github.com/hevertonfreitas/DbUpload
 * Copyright (c) Heverton Coneglian de Freitas <hevertonfreitas1@yahoo.com.br>.
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Heverton Coneglian de Freitas <hevertonfreitas1@yahoo.com.br>
 * @link          https://github.com/hevertonfreitas/DbUpload
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

use Hashids\Hashids;

App::uses('AppHelper', 'View/Helper');

/**
 * Class DbUploadHelper
 *
 * @author Heverton Coneglian de Freitas <hevertonfreitas1@yahoo.com.br>
 */
class DbUploadHelper extends AppHelper
{

    /**
     * Generates a link that points to the contents of the uploaded file
     *
     * @param array $data array of data from the model
     * @param string $field the field where the Behavior is configured
     * @param string $primaryKey the primary key field of the table
     * @return string the url of the uploaded file
     */
    public function uploadUrl($data, $field, $primaryKey = 'id')
    {
        $hashids = new Hashids(Configure::read('Security.salt'));

        list($model, $campo) = explode('.', $field);
        $url = Router::url(
            array(
                'controller' => 'upload',
                'action' => 'download',
                'plugin' => 'dbupload',
                'admin' => false,
                '?' => array(
                    'm' => $model, //model
                    'f' => $campo, //field
                    'i' => $hashids->encode($data[$model][$primaryKey]) //primary key
                )
            )
        );
        return $url;
    }

}

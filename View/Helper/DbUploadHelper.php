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
     * @param string $model The name of the model where the file is stored
     * @param string $field The field containing the file
     * @param string $id The id of the record
     * @return string The URL of the uploaded file
     */
    public function uploadUrl($model, $field, $id)
    {
        $encodedId = new Hashids(Configure::read('Security.salt'));

        $url = array(
            'controller' => 'upload',
            'action' => 'download',
            'plugin' => 'dbupload',
            'admin' => false,
            '?' => array(
                'm' => $model,
                'f' => $field,
                'i' => $encodedId->encode($id)
            )
        );

        return Router::url($url);
    }
}

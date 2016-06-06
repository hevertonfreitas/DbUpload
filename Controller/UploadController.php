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

App::uses('AppController', 'Controller');

/**
 * Class UploadController
 *
 * @author Heverton Coneglian de Freitas <hevertonfreitas1@yahoo.com.br>
 */
class UploadController extends AppController
{

    /**
     * Display the contents of the uploaded file
     */
    public function download()
    {
        $hashids = new Hashids(Configure::read('Security.salt'));
        $pk = $hashids->decode($this->request->query['i'])[0];
        $this->loadModel($this->request->query['m']);
        $data = $this->{$this->request->query['m']}->find('first', array(
            'recursive' => -1,
            'conditions' => array(
                "{$this->request->query['m']}." . $this->{$this->request->query['m']}->primaryKey => $pk
            ),
            'fields' => array(
                "{$this->request->query['m']}.{$this->request->query['f']}_name",
                "{$this->request->query['m']}.{$this->request->query['f']}_type",
                "{$this->request->query['m']}.{$this->request->query['f']}_size",
                "{$this->request->query['m']}.{$this->request->query['f']}_content"
            )
        ));

        header('Content-length: ' . $data[$this->request->query['m']][$this->request->query['f'] . '_size']);
        header('Content-type: ' . $data[$this->request->query['m']][$this->request->query['f'] . '_type']);
        header('Content-Disposition: inline; filename=' . $data[$this->request->query['m']][$this->request->query['f'] . '_name']);
        echo base64_decode($data[$this->request->query['m']][$this->request->query['f'] . '_content']);

        die();
    }
}

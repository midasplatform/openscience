<?php
/*=========================================================================
 MIDAS Server
 Copyright (c) Kitware SAS. 26 rue Louis Guérin. 69100 Villeurbanne, FRANCE
 All rights reserved.
 More information http://www.kitware.com

 Licensed under the Apache License, Version 2.0 (the "License");
 you may not use this file except in compliance with the License.
 You may obtain a copy of the License at

         http://www.apache.org/licenses/LICENSE-2.0.txt

 Unless required by applicable law or agreed to in writing, software
 distributed under the License is distributed on an "AS IS" BASIS,
 WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 See the License for the specific language governing permissions and
 limitations under the License.
=========================================================================*/

/** AnatomicalAreaModelBase */
abstract class Openscience_AlgorithmModelBase extends Openscience_AppModel
{
  /** Constructor */
  public function __construct()
    {
    parent::__construct();
    $this->_name = 'openscience_algorithm';
    $this->_key = 'algorithm_id';
    $this->_module = 'openscience';

    // Add Datasets
    $this->_mainData = array(
      'algorithm_id' => array('type' => MIDAS_DATA),
      'name' => array('type' => MIDAS_DATA),
      'description' => array('type' => MIDAS_DATA),
      'publications' => array('type' => MIDAS_DATA),
      'data' => array('type' => MIDAS_DATA),
      'performance' => array('type' => MIDAS_DATA),
      'dashboard' => array('type' => MIDAS_DATA),
      'sourcecode' => array('type' => MIDAS_DATA),
      'folder_id' => array('type' => MIDAS_DATA),
      'folder' =>  array('type' => MIDAS_MANY_TO_ONE,
                         'model' => 'Folder',
                         'parent_column' => 'folder_id',
                         'child_column' => 'folder_id'),
      'resultsets' => array('type' => MIDAS_MANY_TO_MANY,
                            'model' => 'Resultset',
                            'module' => 'openscience',
                            'table' => 'openscience_algorithm2resultset',
                            'parent_column' => 'algorithm_id',
                            'child_column' => 'resultset_id')
      );
    $this->initialize(); // required
    } // end __construct()

  /**
   * Add a resultset to the algorithm
   * @return void
   */
  abstract function addResultset($algorithm, $resultset);

  abstract function getLatestsResultsets($algorithm, $limit);

}

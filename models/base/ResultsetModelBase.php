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

/** ResultsetModelBase */
abstract class Openscience_ResultsetModelBase extends Openscience_AppModel
{
  /** Constructor */
  public function __construct()
    {
    parent::__construct();
    $this->_name = 'openscience_resultset';
    $this->_key = 'resultset_id';
    $this->_module = 'openscience';

    // Add Datasets
    $this->_mainData = array(
      'resultset_id' => array('type' => MIDAS_DATA),
      'date' => array('type' => MIDAS_DATA),
      'dashboard' => array('type' => MIDASDATA),
      'performance' => array('type' => MIDAS_DATA),
      'data' => array('type' => MIDAS_DATA),
      'folder_id' => array('type' => MIDAS_DATA),
      'contents' => array('type' => MIDAS_DATA),
      'folder' =>  array('type' => MIDAS_MANY_TO_ONE,
                         'model' => 'Folder',
                         'parent_column' => 'folder_id',
                         'child_column' => 'folder_id'),
      );
    $this->initialize(); // required
    } // end __construct()

}

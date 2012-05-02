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
abstract class Openscience_AnatomicalareaModelBase extends Openscience_AppModel
{
  /** Constructor */
  public function __construct()
    {
    parent::__construct();
    $this->_name = 'openscience_anatomicalarea';
    $this->_key = 'anatomicalarea_id';

    $this->_mainData = array(
      'anatomicalarea_id' => array('type' => MIDAS_DATA),
      'name' => array('type' => MIDAS_DATA),
      'description' => array('type' => MIDAS_DATA),
      'algorithms' => array('type' => MIDAS_MANY_TO_MANY,
                            'model' => 'Algorithm',
                            'module' => 'openscience',
                            'table' => 'openscience_anatomicalarea2algorithm',
                            'parent_column' => 'anatomicalarea_id',
                            'child_column' => 'algorithm_id')
      );
    $this->initialize(); // required
    } // end __construct()

}

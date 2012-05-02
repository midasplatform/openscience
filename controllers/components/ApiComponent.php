<?php
/*=========================================================================
MIDAS Server
Copyright (c) Kitware SAS. 20 rue de la Villette. All rights reserved.
69328 Lyon, FRANCE.

See Copyright.txt for details.
This software is distributed WITHOUT ANY WARRANTY; without even
the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
PURPOSE.  See the above copyright notices for more information.
=========================================================================*/

/** Component for api methods */
class Openscience_ApiComponent extends AppComponent
{

  /**
   * Helper function for verifying keys in an input array
   */
  private function _checkKeys($keys, $values)
    {
    foreach($keys as $key)
      {
      if(!array_key_exists($key, $values))
        {
        throw new Exception('Parameter '.$key.' must be set.', -1);
        }
      }
    }

  /**
   * Add a resultset to an algorithm
   * @param algorithmId the id of the algorithm to add a result to
   * @param folderId the id of the folder to be associated with the result set
   * @return a message of success
   */
  public function resultsetAdd($value)
    {
    $this->_checkKeys(array('algorithmId', 'folderId'), $value);

    $algorithmId = $value['algorithmId'];
    $folderId = $value['folderId'];

    $modelLoader = new MIDAS_ModelLoader;
    $folderModel = $modelLoader->loadModel('Folder');
    $algorithmModel = $modelLoader->loadModel('Algorithm', 'openscience');
    $algorithmModel->loadDaoClass('AlgorithmDao', 'openscience');
    $resultsetModel = $modelLoader->loadModel('Resultset', 'openscience');
    $resultsetModel->loadDaoClass('ResultsetDao', 'openscience');

    $componentLoader = new MIDAS_ComponentLoader();
    $linkComponent = $componentLoader->loadComponent('LinkDataToResults',
                                                     'openscience');
    $jsonComponent = $componentLoader->loadComponent('Json');

    $algorithmDao = $algorithmModel->load($algorithmId);

    $resultsetDao = new Openscience_ResultsetDao();
    $resultsetDao->setFolderId($folderId);
    $resultsetDao->setData($algorithmDao->getData());
    $resultsetDao->setDashboard($algorithmDao->getDashboard());
    $contentArray = $linkComponent->getAssociationArray($folderId);
    $contents = jsonComponent::encode($contentArray);
    $resultsetDao->setContents($contents);
    $resultsetDao->setPerformance(0.91);
    $resultsetModel->save($resultsetDao);

    $algorithmDao = $algorithmModel->load($algorithmId);
    $algorithmModel->addResultset($algorithmDao, $resultsetDao);

    $ret = array();
    $ret['message'] = 'success';
    return $ret;
    }
    
} // end class

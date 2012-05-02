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

  private function _mean($array)
    {
    return array_sum($array)/count($array);
    }

// Function to calculate square of value - mean
  private function _sd_square($x, $mean)
    {
    return pow($x - $mean,2);
    }

// Function to calculate standard deviation (uses sd_square)    
  private function _sd($array)
    {
    // square root of sum of squares devided by N-1
      return sqrt(array_sum(array_map(array($this,'_sd_square'), $array, array_fill(0,count($array), (array_sum($array) / count($array)) ) ) ) / (count($array)-1) );
    }

  // Coefficient of variation
  private function _cv($array)
    {
      return $this->_sd($array)/$this->_mean($array);
    }

  // Compute the mean coefficient of variation
  private function _computeMeanCoefficientOfVariation(&$contentArray)
    {
    // aggregate the volume results on a per-patient basis
    $resultArrays = array();
    foreach($contentArray as $dataset)
      {
      if(!array_key_exists('Patient Name', $dataset) ||
         !array_key_exists('Seed 1: Volume', $dataset))
        {
        continue;
        }
      $name = $dataset['Patient Name'];
      $volume = $dataset['Seed 1: Volume'];
      if(array_key_exists($name, $resultArrays))
        {
        $resultArrays[$name][] = $volume;
        }
      else
        {
        $resultArrays[$name] = array();
        $resultArrays[$name][] = $volume;
        }
      }

    // compute the mean of the coefficients of variation
    $coefficientsOfVariation = array();
    foreach($resultArrays as $result)
      {
      $coefficientsOfVariation[] = $this->_cv($result);
      }
    $meanCoefficientOfVariation = $this->_mean($coefficientsOfVariation);
    return $meanCoefficientOfVariation;
    }

  /**
   * Add a resultset to an algorithm
   * @param algorithmId the id of the algorithm to add a result to
   * @param folderId the id of the folder to be associated with the result set
   * @return a message of success
   */
  public function resultsetAdd($value)
    {

    // Verify the paramenters
    $this->_checkKeys(array('algorithmId', 'folderId'), $value);

    // Store the parameters
    $algorithmId = $value['algorithmId'];
    $folderId = $value['folderId'];

    // Load the models and daos needed
    $modelLoader = new MIDAS_ModelLoader;
    $folderModel = $modelLoader->loadModel('Folder');
    $algorithmModel = $modelLoader->loadModel('Algorithm', 'openscience');
    $algorithmModel->loadDaoClass('AlgorithmDao', 'openscience');
    $resultsetModel = $modelLoader->loadModel('Resultset', 'openscience');
    $resultsetModel->loadDaoClass('ResultsetDao', 'openscience');

    // Load the LinkDataToResults component in order to aggregate the results
    // from the folder specified with folderId.
    $componentLoader = new MIDAS_ComponentLoader();
    $linkComponent = $componentLoader->loadComponent('LinkDataToResults',
                                                     'openscience');

    // Load the json component to encode the results from the LinkDataToResults
    // component for later retrieval (keeping the schema simple).
    $jsonComponent = $componentLoader->loadComponent('Json');

    // Get the array of results to items and volumes
    $contentArray = $linkComponent->getAssociationArray($folderId);

    // Get the metric value from the result arrays
    $meanCoefficientOfVariance =
      $this->_computeMeanCoefficientOfVariation($contentArray);

    // Load the specified algorithm
    $algorithmDao = $algorithmModel->load($algorithmId);

    // Create and save the new resultset
    $resultsetDao = new Openscience_ResultsetDao();
    $resultsetDao->setFolderId($folderId);
    $resultsetDao->setData($algorithmDao->getData());
    $resultsetDao->setDashboard($algorithmDao->getDashboard());
    $contents = jsonComponent::encode($contentArray);
    $resultsetDao->setContents($contents);
    $resultsetDao->setPerformance($meanCoefficientOfVariance);
    $resultsetModel->save($resultsetDao);

    // Associate the resultset with the algorithm
    $algorithmDao = $algorithmModel->load($algorithmId);
    $algorithmModel->addResultset($algorithmDao, $resultsetDao);

    // Return a success message
    $ret = array();
    $ret['message'] = 'success';
    return $ret;
    }
    
} // end class

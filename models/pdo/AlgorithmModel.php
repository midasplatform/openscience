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
require_once BASE_PATH.'/modules/openscience/models/base/AlgorithmModelBase.php';

/**
 * Anatomical Area PDO Model
 */
class Openscience_AlgorithmModel extends Openscience_AlgorithmModelBase
{
  /**
   * Return all the records in the table
   * @return Array of Algorithm
   */
  function getAll()
    {
    $sql = $this->database->select();
    $rowset = $this->database->fetchAll($sql);
    $rowsetAnalysed = array();
    foreach($rowset as $keyRow => $row)
      {
      $tmpDao = $this->initDao('Algorithm', $row, 'openscience');
      $rowsetAnalysed[] = $tmpDao;
      }
    return $rowsetAnalysed;
    }

  /**
   * Add a resultset to the algorithm
   * @return void
   */
  function addResultset($algorithm, $resultset)
    {
    if(!$algorithm instanceof Openscience_AlgorithmDao)
      {
      throw new Zend_Exception("Should be an algorithm.");
      }
    if(!$resultset instanceof Openscience_ResultsetDao)
      {
      throw new Zend_Exception("Should be a resultset.");
      }
    $this->database->link('resultsets', $algorithm, $resultset);
    }

  function getLatestsResultsets($algorithm, $limit=7)
    {
    $sql = $this->database->select()
      ->setIntegrityCheck(false)
      ->from(array('r' => 'openscience_resultset'))
      ->join(array('a' => 'openscience_algorithm2resultset'))
      ->where('a.algorithm_id='.$algorithm->getKey())
      ->where('a.resultset_id=r.resultset_id')
      ->order('date DESC')
      ->limit($limit);
    $rowset = $this->database->fetchAll($sql);
    $results = array();
    foreach($rowset as $keyRow => $row)
      {
      $results[] = $this->initDao('Resultset', $row, 'openscience');
      }
    return $results;
    }
}

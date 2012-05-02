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
require_once BASE_PATH.'/modules/openscience/models/base/ResultsetModelBase.php';

/**
 * Anatomical Area PDO Model
 */
class Openscience_ResultsetModel extends Openscience_ResultsetModelBase
{
  /**
   * Return all the records in the table
   * @return Array of Resultset
   */
  function getAll()
    {
    $sql = $this->database->select();
    $rowset = $this->database->fetchAll($sql);
    $rowsetAnalysed = array();
    foreach($rowset as $keyRow => $row)
      {
      $tmpDao = $this->initDao('Resultset', $row, 'openscience');
      $rowsetAnalysed[] = $tmpDao;
      }
    return $rowsetAnalysed;
    }
}

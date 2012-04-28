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
   * Get the name of the requested dashboard
   * @param dashboard_id the id of the dashboard
   * @return the name of the dashboard
   */
  public function getDashboardForDate($value)
    {
    $this->_checkKeys(array('date'), $value);
    $ret = array();
    $ret['test'] = 'Open Science Dashboard';
    }
    
} // end class

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
class Openscience_LinkDataToResultsComponent extends AppComponent
{

  private function findBitstreamNamed($itemDao, $name)
    {
    $modelLoader = new MIDAS_ModelLoader;
    $itemModel = $modelLoader->loadModel('Item');
    $itemRevisionModel = $modelLoader->loadModel('ItemRevision');
    $revisionDao = $itemModel->getLastRevision($itemDao);
    foreach( $revisionDao->getBitstreams() as $bitstreamDao )
      {
      if($bitstreamDao->getName() === $name)
        {
        return $bitstreamDao;
        }
      }
    return NULL;
    }

  private function generateThumbnail($item, $bitstream)
    {
    $modelLoader = new MIDAS_ModelLoader;
    $itemModel = $modelLoader->loadModel('Item');
    $bitstreamModel = $modelLoader->loadModel('Bitstream');
    $bitstreamModel->loadDaoClass('BitstreamDao');
    $assetstoreModel = $modelLoader->loadModel('Assetstore');
    $itemthumbnailModel = $modelLoader->loadModel('Itemthumbnail',
                                                  'thumbnailcreator');
    $itemthumbnailModel->loadDaoClass('ItemthumbnailDao', 'thumbnailcreator');

    $componentLoader = new MIDAS_ComponentLoader();
    $imComponent = $componentLoader->loadComponent('Imagemagick',
                                                   'thumbnailcreator');

    $itemThumbnail = $itemthumbnailModel->getByItemId($item->getKey());

    if(!$itemThumbnail)
      {
      $itemThumbnail = new Thumbnailcreator_ItemthumbnailDao();
      $itemThumbnail->setItemId($item->getKey());
      }
    else
      {
      $oldThumb = $bitstreamModel->load($itemThumbnail->getThumbnailId());
      $bitstreamModel->delete($oldThumb);
      }

    $thumbnail = $imComponent->createThumbnailFromPath($bitstream->getFullPath(),
                                                       575, 0, false);
    if(!file_exists($thumbnail))
      {
      return;
      }

    $thumb = $bitstreamModel->createThumbnail($assetstoreModel->getDefault(),
                                              $thumbnail);
    $itemThumbnail->setThumbnailId($thumb->getKey());
    $itemthumbnailModel->save($itemThumbnail);
    }

  /**
   * Output an multi-array associating data and results
   * @param $resultFolderId the id of the folder containing result items
   * @return the name of the dashboard
   */
  public function getAssociationArray($resultFolderId)
    {

    // Load the models needed
    $modelLoader = new MIDAS_ModelLoader;
    $folderModel = $modelLoader->loadModel('Folder');
    $itemModel = $modelLoader->loadModel('Item');
    $itemRevisionModel = $modelLoader->loadModel('ItemRevision');
    $metadataModel = $modelLoader->loadModel('Metadata');
    $itemThumbnailModel = $modelLoader->loadModel('Itemthumbnail',
                                                  'thumbnailcreator');
    

    // This needs to be optimized. Right now we're doing a O(n^2) operation when
    // we should be able to do this in O(n). This should be fixed.
    // TODO FIXME
    $resultFolderDao = $folderModel->load($resultFolderId);
    $resultItems = $this->findItemsAtTheBottomOfTheHierarchy($resultFolderDao);
    $ret = array();
    foreach($resultItems as $curItem)
      {

      // generate the thumbnail
      $axialBitstream = $this->findBitstreamNamed($curItem, 'tumor_2.png');
      $this->generateThumbnail($curItem, $axialBitstream);

      $result = array();
      $revisionDao = $itemModel->getLastRevision($curItem);
      $metadata = $itemRevisionModel->getMetadata($revisionDao);
      $thumbnailId = $itemThumbnailModel->getByItemId($curItem->getKey())->getKey();
      $result['itemthumbnail'] = $thumbnailId;
      foreach($metadata as $metadataDao)
        {
        $element = $metadataDao->getElement();
        $value = $metadataDao->getValue();
        if($element === 'Source Item')
          {
          $sourceItem = $itemModel->load($value);
          $result = array_merge($result, $this->getNameFromSourceItem($sourceItem));
          }
        else if($element === 'Seed 1: Volume')
          {
          $result[$element] = $value;
          }
        }
      $result['item_id'] = $curItem->getKey();
      $ret[] = $result;
      }
    return $ret;
    }

  protected function getNameFromSourceItem($itemDao)
    {
    $modelLoader = new MIDAS_ModelLoader;
    $itemModel = $modelLoader->loadModel('Item');    
    $itemRevisionModel = $modelLoader->loadModel('ItemRevision');
    $metadataModel = $modelLoader->loadModel('Metadata');

    $itemName = $itemDao->getName();
    $uid = '';
    $patienName = '';

    $revisionDao = $itemModel->getLastRevision($itemDao);
    $metadata = $itemRevisionModel->getMetadata($revisionDao);
    foreach($metadata as $metadataDao)
      {
      $element = $metadataDao->getElement();
      $qualifier = $metadataDao->getQualifier();
      $value = $metadataDao->getValue();
      if( $element === 'DICOM' && $qualifier === 'SeriesInstanceUID')
        {
        $uid = $value;
        }
      else if( $element === 'DICOM' && ($qualifier === 'PatientsName' ||
                                        $qualifier === 'PatientName'))
        {
        $patientName = $value;
        }
      }
    $ret = array();
    $ret['Patient Name'] = $patientName;
    $ret['Series'] = $uid;
    $ret['Timestamp'] = $itemName;
    return $ret;
    }

  /**
   * Recursively walks a hierarchy to get all the items within it.
   * @return an array of all of the items within a hierarchy
   */
  private function findItemsAtTheBottomOfTheHierarchy($folderDao)
    {
    $ret = $folderDao->getItems();
    $childFolders = $folderDao->getFolders();
    foreach($childFolders as $curFolder)
      {
      $children = $this->findItemsAtTheBottomOfTheHierarchy($curFolder);
      $ret = array_merge($ret, $children);
      }
    return $ret;
    }
    
} // end class

<?php
namespace Craft;

/**
 * Pimp My Matrix by Supercool
 *
 * @package   PimpMyMatrix
 * @author    Josh Angell
 * @copyright Copyright (c) 2014, Supercool Ltd
 * @link      http://www.supercooldesign.co.uk
 */

class PimpMyMatrixService extends BaseApplicationComponent
{

  private $_matrixFieldIds;


  /**
   * Returns an array of all the Matrix field ids
   * @return array
   */
  public function getMatrixFieldIds()
  {

    if (!$this->_matrixFieldIds)
    {
      $this->_matrixFieldIds = craft()->db->createCommand()
        ->select('id')
        ->from('fields')
        ->where('type = :type', array(':type' => 'Matrix'))
        ->queryColumn();
    }

    return $this->_matrixFieldIds;

  }

  /**
   * Returns an array of Matrix fields
   * @return array
   */
  public function getMatrixFields()
  {

    $return = array();

    foreach ($this->getMatrixFieldIds() as $fieldId)
    {
      $return[] = craft()->fields->getFieldById($fieldId);
    }

    return $return;

  }

}

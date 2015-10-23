<?php
namespace Craft;

/**
 * Pimp My Matrix by Supercool
 *
 * @package   PimpMyMatrix
 * @author    Josh Angell
 * @copyright Copyright (c) 2015, Supercool Ltd
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

  /**
   *
   */
  public function loadConfigurator($container, $context)
  {
    craft()->templates->includeCssFile('//fonts.googleapis.com/css?family=Coming+Soon');
    craft()->templates->includeCssResource('pimpmymatrix/css/pimpmymatrix.css');
    craft()->templates->includeJsResource('pimpmymatrix/js/blocktypefieldlayoutdesigner.js');
    craft()->templates->includeJsResource('pimpmymatrix/js/groupsdesigner.js');
    craft()->templates->includeJsResource('pimpmymatrix/js/configurator.js');

    $settings = array(
      'matrixFieldIds' => craft()->pimpMyMatrix->getMatrixFieldIds(),
      'context' => $context
    );

    craft()->templates->includeJs('new PimpMyMatrix.Configurator("'.$container.'", '.JsonHelper::encode($settings).');');
  }

  /**
   *
   */
  public function loadFieldManipulator($context)
  {

    // Get global data
    $globalPimpedBlockTypes = craft()->pimpMyMatrix_blockTypes->getBlockTypesByContext('global', 'context');

    // Get all the data for the entrytype context regardless of entrytype id
    $mainContext = explode(':', $context)[0];
    $contextPimpedBlockTypes = craft()->pimpMyMatrix_blockTypes->getBlockTypesByContext($mainContext, 'context', true);

    $pimpedBlockTypes = array_merge($globalPimpedBlockTypes, $contextPimpedBlockTypes);

    if ($pimpedBlockTypes)
    {
      craft()->templates->includeCssResource('pimpmymatrix/css/pimpmymatrix.css');

      // Set up the groups
      craft()->templates->includeJsResource('pimpmymatrix/js/fieldmanipulator.js');
      $settings = array(
        'blockTypes' => $pimpedBlockTypes,
        'context' => $context
      );
      craft()->templates->includeJs('new PimpMyMatrix.FieldManipulator('.JsonHelper::Encode($settings).');');
    }

  }

}

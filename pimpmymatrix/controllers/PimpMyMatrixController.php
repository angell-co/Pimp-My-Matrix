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

class PimpMyMatrixController extends BaseController
{

  /**
   * @inheritDoc BaseController::init()
   *
   * @throws HttpException
   * @return null
   */
  public function init()
  {
    craft()->userSession->requireAdmin();
  }

  /**
   * Returns the html for the block type grouping field layout designer.
   *
   * @method actionGetConfigurator
   * @return json
   */
  public function actionGetConfigurator()
  {

    $this->requirePostRequest();
    $this->requireAjaxRequest();

    $fieldId = craft()->request->getParam('fieldId');
    $field = craft()->fields->getFieldById($fieldId);
    $blockTypes = craft()->matrix->getBlockTypesByFieldId($fieldId);

    $blockTypeIds = array();
    foreach ($blockTypes as $blockType)
    {
      $blockTypeIds[] = $blockType->id;
    }

    $context = craft()->request->getParam('context');

    $existingBlockTypeGroups = craft()->pimpMyMatrix_blockTypeGroups->getBlockTypeGroupsByContext($context, 'tabName');

    $fld = craft()->templates->render('pimpmymatrix/flds/configurator', array(
      'matrixField' => $field,
      'blockTypes' => $blockTypes,
      'blockTypeIds' => $blockTypeIds,
      'existingBlockTypeGroups' => $existingBlockTypeGroups
    ));

    $this->returnJson(array(
      'html' => $fld
    ));

  }

}

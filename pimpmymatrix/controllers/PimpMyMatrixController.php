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
   * Loads the edit page for the global context.
   * Used in the plugin cp section.
   */
  public function actionEditGlobalContext()
  {

    $variables['matrixFields'] = craft()->pimpMyMatrix->getMatrixFields();

    $variables['globalPimpedBlockTypes'] = craft()->pimpMyMatrix_blockTypes->getBlockTypesByContext('global', 'fieldId', true);

    craft()->pimpMyMatrix->loadConfigurator('#pimpmymatrix-global-context-table', 'global');

    $this->renderTemplate('pimpmymatrix/_index', $variables);

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

    $pimpedBlockTypes = craft()->pimpMyMatrix_blockTypes->getBlockTypesByContext($context, 'groupName', false, $fieldId);

    $fld = craft()->templates->render('pimpmymatrix/flds/configurator', array(
      'matrixField' => $field,
      'blockTypes' => $blockTypes,
      'blockTypeIds' => $blockTypeIds,
      'pimpedBlockTypes' => $pimpedBlockTypes
    ));

    $this->returnJson(array(
      'html' => $fld
    ));

  }

  /**
   * Returns the html for the individual block type field layout designer.
   *
   * @method actionGetFieldsConfigurator
   * @return json
   */
  public function actionGetFieldsConfigurator()
  {

    $this->requirePostRequest();
    $this->requireAjaxRequest();

    $context = craft()->request->getParam('context');
    $blockTypeId = craft()->request->getParam('blockTypeId');
    $pimpedBlockType = craft()->pimpMyMatrix_blockTypes->getBlockType($context, $blockTypeId);
    $fieldLayout = $pimpedBlockType->getFieldLayout();

    $fld = craft()->templates->render('pimpmymatrix/flds/fields', array(
      'pimpedBlockType' => $pimpedBlockType,
      'fieldLayout' => $fieldLayout
    ));

    $this->returnJson(array(
      'html' => $fld
    ));

  }

}

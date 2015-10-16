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

  public function actionGetConfigurator()
  {

    $this->requirePostRequest();
    $this->requireAjaxRequest();

    $fieldId = craft()->request->getParam('fieldId');

    $fld = craft()->templates->render('pimpmymatrix/flds/configurator', array(
      'fieldId' => $fieldId
    ));

    $this->returnJson(array(
      'html' => $fld
    ));

  }

}

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

  public function actionGetBlocks()
  {
    // Only ajax post requests
    $this->requirePostRequest();
    $this->requireAjaxRequest();

    // get elemendIds from POST
    $elemendIds = craft()->request->getPost('elemendIds');

    $criteria = craft()->elements->getCriteria(ElementType::MatrixBlock);
    $criteria->id = $elemendIds;
    $blocks = $criteria->find();


    // return blockTypeIdsByElementId or false
    if ( $blocks ) {
      $this->returnJson(array(
        'success' => true,
        'blocks' => $blocks
      ));
    } else {
      $this->returnJson(array(
        'success' => false
      ));
    }
  }

}

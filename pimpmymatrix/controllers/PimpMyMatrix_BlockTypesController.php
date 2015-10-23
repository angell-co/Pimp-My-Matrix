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

class PimpMyMatrix_BlockTypesController extends BaseController
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
	 * Saves a pimped block type
	 */
	public function actionSaveBlockTypes()
	{

		$this->requirePostRequest();
		$this->requireAjaxRequest();

		// This will be an array of Tab Names with Block Type IDs.
		// The order in which they appear is the order in which they should also
		// be returned in eventually, so we will just rely on the id to describe this
		// and make sure each time we are referencing a context that already exists to
		// delete the rows matching that context before proceeding with the save.
		$blockTypesPostData = craft()->request->getPost('pimpedBlockTypes');

		$context = craft()->request->getPost('context');
		$fieldId = craft()->request->getPost('fieldId');

		// Get any existing field layouts so we don’t lose them
		$fieldLayoutIds = craft()->pimpMyMatrix_blockTypes->getFieldLayoutIds($context, $fieldId);

		// Remove all current block types by context
		craft()->pimpMyMatrix_blockTypes->deleteBlockTypesByContext($context, $fieldId);

		// Loop over the data and save new rows for each block type / group combo
		$errors = 0;
		if (is_array($blockTypesPostData))
		{
			foreach ($blockTypesPostData as $groupName => $blockTypeIds)
			{
				foreach ($blockTypeIds as $blockTypeId)
				{
					$pimpedBlockType = new PimpMyMatrix_BlockTypeModel();
					$pimpedBlockType->fieldId           = $fieldId;
					$pimpedBlockType->matrixBlockTypeId = $blockTypeId;
					$pimpedBlockType->fieldLayoutId     = isset($fieldLayoutIds[$blockTypeId]) ? $fieldLayoutIds[$blockTypeId] : null;
					$pimpedBlockType->groupName         = urldecode($groupName);
					$pimpedBlockType->context           = $context;

					$success = craft()->pimpMyMatrix_blockTypes->saveBlockType($pimpedBlockType);
					if (!$success)
					{
						$errors++;
					}
				}
			}
		}


		if ($errors > 0)
		{
			$this->returnJson(array(
				'success' => false
			));
		}
		else
		{
			$this->returnJson(array(
				'success' => true
			));
		}

	}


	/**
	 * Delete a set of pimped block types for a given field and context
	 */
	public function actionDeleteBlockTypes()
	{
		$this->requirePostRequest();
		$this->requireAjaxRequest();

		$context = craft()->request->getPost('context');
		$fieldId = craft()->request->getPost('fieldId');

		if (craft()->pimpMyMatrix_blockTypes->deleteBlockTypesByContext($context, $fieldId))
		{
			$this->returnJson(array(
				'success' => true
			));
		}
		else
		{
			$this->returnJson(array(
				'success' => false
			));
		}

	}


	/**
	 * Saves a field layout for a given pimped block type
	 */
	public function actionSaveFieldLayout()
	{

		$this->requirePostRequest();
		$this->requireAjaxRequest();

		$pimpedBlockTypeId = craft()->request->getPost('pimpedBlockTypeId');
		$blockTypeFieldLayouts = craft()->request->getPost('blockTypeFieldLayouts');

		if ($pimpedBlockTypeId)
		{

			$pimpedBlockTypeRecord = PimpMyMatrix_BlockTypeRecord::model()->findById($pimpedBlockTypeId);

			if (!$pimpedBlockTypeRecord)
			{
				throw new Exception(Craft::t('No PimpMyMatrix block type exists with the ID “{id}”', array('id' => $pimpedBlockTypeId)));
			}

			$pimpedBlockType = PimpMyMatrix_BlockTypeModel::populateModel($pimpedBlockTypeRecord);

		}
		else
		{
			return false;
		}

		// Set the field layout on the model
		$postedFieldLayout = craft()->request->getPost('blockTypeFieldLayouts', array());
		$assembledLayout = craft()->fields->assembleLayout($postedFieldLayout, array());
		$pimpedBlockType->setFieldLayout($assembledLayout);

		// Save it
		if (craft()->pimpMyMatrix_blockTypes->saveFieldLayout($pimpedBlockType))
		{
			$this->returnJson(array(
				'success' => true
			));
		}
		else
		{
			$this->returnJson(array(
				'success' => false
			));
		}

	}

}

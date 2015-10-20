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
	 * Saves a layout
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

		// Remove all current block types by context
		// TODO: here we need to save out an array of the field layouts
		//       that are attached to the current block types in this context
		//       so that we don’t lose them when saving.
		craft()->pimpMyMatrix_blockTypes->deleteBlockTypesByContext($context, $fieldId);

		// Loop over the data and save new rows for each block type / group combo
		if (is_array($blockTypesPostData))
		{
			foreach ($blockTypesPostData as $groupName => $blockTypeIds)
			{
				foreach ($blockTypeIds as $blockTypeId)
				{
					$pimpedBlockType = new PimpMyMatrix_BlockTypeModel();
					$pimpedBlockType->fieldId           = $fieldId;
					$pimpedBlockType->matrixBlockTypeId = $blockTypeId;
					$pimpedBlockType->groupName         = urldecode($groupName);
					$pimpedBlockType->context           = $context;
					// TODO: attach field layout here

					// TODO: Catch errors
					craft()->pimpMyMatrix_blockTypes->saveBlockType($pimpedBlockType);
				}
			}
		}

		$this->returnJson(array(
			'success' => true
		));

	}

	// /**
	//  * [editFieldLayout description]
	//  */
	// public function actionEditFieldLayout(array $variables = array())
	// {
	//
	// 	// get the performance group
	// 	if (!empty($variables['performanceGroupId']))
	// 	{
	//
	// 		$variables['performanceGroup'] = craft()->boxOffice_performanceGroups->getPerformanceGroupById($variables['performanceGroupId']);
	//
	// 		if (!$variables['performanceGroup'])
	// 		{
	// 			throw new HttpException(404);
	// 		}
	//
	// 		$variables['title'] = $variables['performanceGroup']->name . ' — ' . $variables['fieldLayoutType'];
	// 	}
	// 	else
	// 	{
	// 		throw new HttpException(404);
	// 	}
	//
	//
	// 	// Get all the field layouts for this group
	// 	$fieldLayoutIds = craft()->db->createCommand()
	// 		->select('fieldLayoutId')
	// 		->from('boxoffice_fieldlayouts')
	// 		->where('performanceGroupId = :performanceGroupId', array(':performanceGroupId' => $variables['performanceGroup']->id))
	// 		->queryColumn();
	//
	// 	// Get the actual performance or instance field layout for this group
	// 	// depending on the url we're on, and the weird BoxOffice_FieldLayout as well
	// 	if ($fieldLayoutIds)
	// 	{
	// 		switch ($variables['fieldLayoutType']) {
	// 			case 'performances':
	// 				$fieldLayoutType = BoxOfficeElementType::Performance;
	// 				break;
	//
	// 			case 'instances':
	// 				$fieldLayoutType = BoxOfficeElementType::Instance;
	// 				break;
	// 		}
	//
	// 		$fieldLayoutId = craft()->db->createCommand()
	// 			->select('id')
	// 			->from('fieldlayouts')
	// 			->where('type = :type', array(':type' => $fieldLayoutType))
	// 			->andWhere(array('and', array('in', 'id', $fieldLayoutIds)))
	// 			->queryScalar();
	// 	}
	//
	// 	$variables['fieldLayoutId'] = $fieldLayoutId;
	//
	// 	$variables['fieldLayout'] = null;
	//
	// 	if ( $fieldLayoutId )
	// 	{
	// 		$variables['fieldLayout'] = craft()->fields->getLayoutById($fieldLayoutId);
	//
	// 		$boxOfficeFieldLayoutId = craft()->db->createCommand()
	// 			->select('id')
	// 			->from('boxoffice_fieldlayouts')
	// 			->where('fieldLayoutId = :fieldLayoutId', array(':fieldLayoutId' => $fieldLayoutId))
	// 			->queryScalar();
	//
	// 		if ( $boxOfficeFieldLayoutId )
	// 		{
	// 			$variables['boxOfficeFieldLayoutId'] = $boxOfficeFieldLayoutId;
	// 		}
	// 	}
	//
	// 	$variables['crumbs'] = array(
	// 		array('label' => Craft::t('Box Office'), 'url' => UrlHelper::getUrl('boxoffice')),
	// 		array('label' => Craft::t('Performance Groups'), 'url' => UrlHelper::getUrl('boxoffice/performance-groups')),
	// 	);
	//
	// 	$this->renderTemplate('boxoffice/performance-groups/_edit-field-layout', $variables);
	// }


	/**
	 *
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

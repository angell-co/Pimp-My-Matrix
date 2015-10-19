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

class PimpMyMatrix_BlockTypeGroupsService extends BaseApplicationComponent
{

	/**
	 * Saves a block type group
	 *
	 * @method saveBlockTypeGroup
	 * @param  PimpMyMatrix_BlockTypeGroupModel $blockTypeGroup
	 * @throws \Exception
	 * @return bool
	 */
	public function saveBlockTypeGroup(PimpMyMatrix_BlockTypeGroupModel $blockTypeGroup)
	{

		$blockTypeGroupRecord = new PimpMyMatrix_BlockTypeGroupRecord();

		$blockTypeGroupRecord->matrixBlockTypeId = $blockTypeGroup->matrixBlockTypeId;
		$blockTypeGroupRecord->tabName           = $blockTypeGroup->tabName;
		$blockTypeGroupRecord->context           = $blockTypeGroup->context;

		$blockTypeGroupRecord->validate();
		$blockTypeGroup->addErrors($blockTypeGroupRecord->getErrors());


		if (!$blockTypeGroup->hasErrors())
		{
			$transaction = craft()->db->getCurrentTransaction() === null ? craft()->db->beginTransaction() : null;
			try
			{

				// Save it!
				$blockTypeGroupRecord->save(false);

				// TODO: probably don’t need this
				// // Might as well update our cache of the performance group while we have it.
				// $this->_performanceGroupsById[$performanceGroup->id] = $performanceGroup;


				if ($transaction !== null)
				{
					$transaction->commit();
				}
			}
			catch (\Exception $e)
			{
				if ($transaction !== null)
				{
					$transaction->rollback();
				}

				throw $e;
			}

			return true;
		}
		else
		{
			return false;
		}
	}


	/**
	 * Deletes all the groups for a given context
	 *
	 * @param string $context
	 * @throws \Exception
	 * @return bool
	 */
	public function deleteBlockTypeGroupsByContext($context = false)
	{

		if (!$context)
		{
			return false;
		}

		$transaction = craft()->db->getCurrentTransaction() === null ? craft()->db->beginTransaction() : null;
		try
		{

			$affectedRows = craft()->db->createCommand()->delete('pimpmymatrix_blocktypegroups', array('context' => $context));

			if ($transaction !== null)
			{
				$transaction->commit();
			}

			return (bool) $affectedRows;
		}
		catch (\Exception $e)
		{
			if ($transaction !== null)
			{
				$transaction->rollback();
			}

			throw $e;
		}

	}

}

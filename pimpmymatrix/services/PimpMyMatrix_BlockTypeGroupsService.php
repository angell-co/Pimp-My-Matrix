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

	private $_blockTypeGroupsByContext;

	/**
	 * Returns a block type group by its context.
	 *
	 * @param $context
	 * @param $groupBy          Group by an optional model attribute to group by
	 * @param $ignoreSubContext Optionally ignore the sub context (id)
	 * @return array
	 */
	public function getBlockTypeGroupsByContext($context, $groupBy = false, $ignoreSubContext = false)
	{

		if ($ignoreSubContext)
		{
			$blockTypeGroupRecords = PimpMyMatrix_BlockTypeGroupRecord::model()->findAll(array(
				'condition' => "context LIKE '{$context}%'"
			));
		}
		else
		{
			$blockTypeGroupRecords = PimpMyMatrix_BlockTypeGroupRecord::model()->findAllByAttributes(array(
				'context' => $context
			));
		}

		if ($blockTypeGroupRecords)
		{

			$blockTypeGroups = array();

			foreach ($blockTypeGroupRecords as $blockTypeGroupRecord)
			{
				$blockTypeGroup = $this->_populateBlockTypeGroupFromRecord($blockTypeGroupRecord);
				$this->_blockTypeGroupsByContext[$context][$blockTypeGroup->id] = $blockTypeGroup;
			}

		}
		else
		{
			return null;
		}

		if ($groupBy)
		{
			$return = array();

			foreach ($this->_blockTypeGroupsByContext[$context] as $blockTypeGroup)
			{
				$return[$blockTypeGroup->$groupBy][] = $blockTypeGroup;
			}
			return $return;
		}
		else
		{
			return $this->_blockTypeGroupsByContext[$context];
		}

	}

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

				// Might as well update our cache of the block type group while we have it.
				$this->_blockTypeGroupsByContext[$blockTypeGroup->context] = $blockTypeGroup;

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

	// Private Methods
	// =========================================================================

	/**
	 * Populates a PimpMyMatrix_BlockTypeGroupModel with attributes from a PimpMyMatrix_BlockTypeGroupRecord.
	 *
	 * @param PimpMyMatrix_BlockTypeGroupRecord|null
	 *
	 * @return PimpMyMatrix_BlockTypeGroupModel|null
	 */
	private function _populateBlockTypeGroupFromRecord($blockTypeGroupRecord)
	{
		if (!$blockTypeGroupRecord)
		{
			return null;
		}

		$blockTypeGroup = PimpMyMatrix_BlockTypeGroupModel::populateModel($blockTypeGroupRecord);

		return $blockTypeGroup;
	}

}

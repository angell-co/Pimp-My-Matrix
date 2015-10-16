<?php
namespace Craft;

/**
 * The class name is the UTC timestamp in the format of mYYMMDD_HHMMSS_pluginHandle_migrationName
 */
class m151016_151424_pimpmymatrix_add_block_type_layouts_table extends BaseMigration
{
	/**
	 * Any migration code in here is wrapped inside of a transaction.
	 *
	 * @return bool
	 */
	public function safeUp()
	{
		// Create the craft_pimpmymatrix_blocktypelayouts table
		craft()->db->createCommand()->createTable('pimpmymatrix_blocktypelayouts', array(
			'matrixBlockTypeId' => array('column' => 'integer', 'required' => true),
			'tabName'           => array('required' => true),
			'context'           => array('required' => true),
		), null, true);

		// Add foreign keys to craft_pimpmymatrix_blocktypelayouts
		craft()->db->createCommand()->addForeignKey('pimpmymatrix_blocktypelayouts', 'matrixBlockTypeId', 'matrixblocktypes', 'id', 'CASCADE', null);

		return true;
	}
}

<?php
/**
 * Hitachi Capital Pay By Finance
 *
 * Hitachi Capital Pay By Finance Extension
 *
 * PHP version >= 5.4.*
 *
 * @category  HC
 * @package   PayByFinance
 * @author    Cohesion Digital <support@cohesiondigital.co.uk>
 * @copyright 2014 Hitachi Capital
 * @license   http://www.gnu.org/copyleft/gpl.html GPL License
 * @link      http://www.cohesiondigital.co.uk/
 *
 */

$updater = $this;      // $this is class Mage_Eav_Model_Entity_Setup
$updater->startSetup();

$updater->run(
    "
ALTER TABLE {$this->getTable('sales_flat_order')}
MODIFY COLUMN `finance_status` varchar(24);

ALTER TABLE {$this->getTable('sales_flat_order')}
ADD `finance_total_added` SMALLINT(5) UNSIGNED NULL;
    "
);
$updater->endSetup();

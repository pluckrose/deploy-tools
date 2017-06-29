<?php

abstract class ECS_IntegrationUtils_Model_Import_Abstract extends Varien_Object
{
    protected $_type;
    protected $_eventPrefix = 'abstract';
    protected $_name = 'Import';
    protected $_collection = null;
    protected $_logFile = 'integrationutils.log';
    protected $_startTime = null;
    protected $_endTime = null;
    protected $_read = null;
    protected $_write = null;
    protected $_limit = 0;
    protected $_successCount = 0;
    protected $_failureCount = 0;
    protected $_successMessage = '%d record(s) updated';
    protected $_failureMessage = '%d record(s) failed to update';

    abstract protected function _importRecord($item);
    abstract protected function _deleteRecord($item);

    protected function _init($type, $name)
    {
        $this->_type = $type;
        $this->_name = $name;
    }

    protected function _log($message, $jobStatus = null)
    {
        if(!$this->_endTime) {
            $this->_endTime = time();
        }

        Mage::log($message, null, $this->_logFile, true);
//        echo $message . "\n";
//        flush();
//        @ob_flush();

        if($jobStatus !== null) {
            $logModel = Mage::getModel('integrationutils/log')
                ->setStartedAt($this->_startTime)
                ->setFinishedAt($this->_endTime)
                ->setMessage($message)
                ->setSuccessCount($this->_successCount)
                ->setFailCount($this->_failureCount)
                ->setJobStatus($jobStatus);

            $logModel->save();
        }
    }

    protected function _getEntityTypeByCode($entityCode)
    {
        $group = strstr($entityCode, '_', true);
        $type = substr(strstr($entityCode, '_'), 1);
        return $group . '/' . $type;
    }

    protected function _getCollection()
    {
        if (!$this->_collection) {
            /** @var $collection Mage_Core_Model_Resource_Db_Collection_Abstract */
            $collection = Mage::getModel($this->_type)
                ->getCollection();

            $collection->addFieldToFilter('import_state', ECS_IntegrationUtils_Helper_Data::IMPORT_STATUS_TODO);

            if ($this->_limit) {
                $collection->getSelect()->limit($this->_limit);
            }

            // Allow project specific customizations to collection loading
            Mage::dispatchEvent($this->_eventPrefix . '_integrationutils_import_collection_prepare', array('collection' => $collection));

            $this->_collection = $collection;
        }

        return $this->_collection;
    }

    /**
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    protected function _getRead()
    {
        if (!$this->_read) {
            $this->_read = Mage::getSingleton('core/resource')->getConnection('core_read');
        }

        return $this->_read;
    }

    /**
     * @return Varien_Db_Adapter_Pdo_Mysql
     */
    protected function _getWrite()
    {
        if (!$this->_write) {
            $this->_write = Mage::getSingleton('core/resource')->getConnection('core_write');
        }

        return $this->_write;
    }

    protected function _getTableName($modelEntity)
    {
        return Mage::getSingleton('core/resource')->getTableName($modelEntity);
    }

    /**
     * @return ECS_IntegrationUtils_Helper_Data
     */
    public function helper()
    {
        return Mage::helper('integrationutils');
    }

    final public function import($aoe, $limit = 40)
    {
        $this->_limit = $limit;
//        $this->_log($this->_name . ' started');

        if ($this->_limit) {
            $this->_log($this->helper()->__('Maximum number of records to be processed: %d', $this->_limit));
        }

        $logged = false;

        /** @var ECS_IntegrationUtils_Model_Abstract $item */
        foreach ($this->_getCollection() as $item) {
            if (!$logged) {
                $this->_log('');
                $logged = true;
            }

            switch ($item->getImportState()) {
                case ECS_IntegrationUtils_Helper_Data::IMPORT_STATUS_TODO:
                    try {
                        if ($this->_importRecord($item)) {
                            $item->setImportState(ECS_IntegrationUtils_Helper_Data::IMPORT_STATUS_DONE);
                            $item->save();
                            $this->_successCount++;
                        } else {
                            $this->_failureCount++;
                        }
                    } catch (Exception $e) {
                        $this->_log('ERROR: ' . $e->getMessage());
                        $this->_failureCount++;
                    }
                    break;
                case ECS_IntegrationUtils_Helper_Data::IMPORT_STATUS_DONE:
                    try {
//                        if ($this->_deleteRecord($item)) {
//                            $item->setImportState(ECS_IntegrationUtils_Helper_Data::IMPORT_STATUS_DELETED);
//                            $item->save();
//                            $this->_successCount++;
//                        } else {
//                            $this->_failureCount++;
//                        }
                    } catch (Exception $e) {
                        $this->_log('ERROR: ' . $e->getMessage());
                        $this->_failureCount++;
                    }
                    break;
                default:
                    $this->logMessage("Unrecognised Import State - Sku: ". $item->getSku());
            }
        }

        if ($logged) {
            $this->_log('');
        }

        $this->_log($this->_name . ' complete');
    }

    final public function run($limit = 0)
    {
        $this->_startTime = time();
        try {
            $this->import($limit);

            $this->_endTime = time();

            if ($this->_successCount == 0 && $this->_failureCount == 0) {
                $this->_log(null, "", ECS_IntegrationUtils_Helper_Data::STATUS_NOTHING_TO_DO);
            } elseif ($this->_successCount > 0 && $this->_failureCount > 0) {
                $this->_log(
                    null,
                    $this->helper()->__($this->_successMessage, $this->_successCount)." -|- ".$this->helper()->__($this->_failureMessage, $this->_failureCount),
                    ECS_IntegrationUtils_Helper_Data::STATUS_EXCEPTIONS
                );
            } elseif ($this->_successCount > 0) {
                $this->_log(
                    null,
                    $this->helper()->__($this->_successMessage, $this->_successCount),
                    ECS_IntegrationUtils_Helper_Data::STATUS_SUCCEEDED
                );
            } else {
                $this->_log(
                    null,
                    $this->helper()->__($this->_failureMessage, $this->_failureCount),
                    ECS_IntegrationUtils_Helper_Data::STATUS_FAILED
                );
            }
        } catch (Exception $e) {
            $this->_log(
                null,
                $e->getMessage(),
                ECS_IntegrationUtils_Helper_Data::STATUS_FAILED
            );
        }
    }


}
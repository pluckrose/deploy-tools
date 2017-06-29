<?php

class ECS_IntegrationUtils_Helper_Data extends Mage_Core_Helper_Abstract
{
    const IMPORT_STATUS_TODO = 0;
    const IMPORT_STATUS_DONE = 1;
    const IMPORT_STATUS_DELETED = 2;

    const STATUS_NONE = 0;
    const STATUS_SUCCEEDED = 1;
    const STATUS_FAILED = 2;
    const STATUS_EXCEPTIONS = 3;
    const STATUS_NOTHING_TO_DO = 4;
}
<?php
/**
 * Copyright (c) 2009-2014 Vaimo AB
 *
 * Vaimo reserves all rights in the Program as delivered. The Program
 * or any portion thereof may not be reproduced in any form whatsoever without
 * the written consent of Vaimo, except as provided by licence. A licence
 * under Vaimo's rights in the Program may be available directly from
 * Vaimo.
 *
 * Disclaimer:
 * THIS NOTICE MAY NOT BE REMOVED FROM THE PROGRAM BY ANY USER THEREOF.
 * THE PROGRAM IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS
 * OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL
 * THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE PROGRAM OR THE USE OR OTHER DEALINGS
 * IN THE PROGRAM.
 *
 * @category    Vaimo
 * @package     Vaimo_Module
 * @copyright   Copyright (c) 2009-2014 Vaimo AB
 */

class ECS_CmsPicker_Model_Source_Cms extends Mage_Eav_Model_Entity_Attribute_Source_Abstract {

    public function getAllOptions()
    {
        /* @var $collection ECS_CmsPicker_Model */
        $collection = Mage::getModel('cms/resource_block_collection');
        $collection->addFieldToSelect(array('identifier','title'))
                ->addFieldToFilter('identifier', array('like' => 'product_banner%'));
        $collection->load();
        $result = array();

        foreach($collection as $cmsBlock)
        {
            $result[] = array(
                    'value' => $cmsBlock->getBlockId(),
                    'label' => $cmsBlock->getTitle()
            );
        }
        array_unshift($result, '');
        return $result;
    }
}
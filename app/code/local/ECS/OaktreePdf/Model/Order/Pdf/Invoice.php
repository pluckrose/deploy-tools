<?php

class ECS_OaktreePdf_Model_Order_Pdf_Invoice extends Mage_Sales_Model_Order_Pdf_Invoice
{
    public function getPdf($invoices = array())
    {
        $this->_beforeGetPdf();
        $this->_initRenderer('invoice');

        $pdf = new Zend_Pdf();
        $this->_setPdf($pdf);
        $style = new Zend_Pdf_Style();
        $this->_setFontBold($style, 10);

        foreach ($invoices as $invoice) {
            if ($invoice->getStoreId()) {
                Mage::app()->getLocale()->emulate($invoice->getStoreId());
                Mage::app()->setCurrentStore($invoice->getStoreId());
            }
            $page  = $this->newPage();
            $order = $invoice->getOrder();
            /* Add image */
            $this->insertLogo($page, $invoice->getStore());
            /* Add address */
            $this->insertAddress($page, $invoice->getStore());
            /* Add head */
            $this->insertOrder(
                $page,
                $order,
                Mage::getStoreConfigFlag(self::XML_PATH_SALES_PDF_INVOICE_PUT_ORDER_ID, $order->getStoreId())
            );
            /* Add document text and number */
            $this->insertDocumentNumber(
                $page,
                Mage::helper('sales')->__('Invoice # ') . $invoice->getIncrementId()
            );

            $this->drawDeliveryNote($page, $order);


            /* Add table */
            $this->_drawHeader($page);

            /* Add body */
            foreach ($invoice->getAllItems() as $item){
                if ($item->getOrderItem()->getParentItem()) {
                    continue;
                }
                /* Draw item */
                $this->_drawItem($item, $page, $order);
                $page = end($pdf->pages);
            }

            /* Add totals */
            $this->insertTotals($page, $invoice);
            if ($invoice->getStoreId()) {
                Mage::app()->getLocale()->revert();
            }


        }
        $this->_afterGetPdf();
        return $pdf;
    }


    public function drawDeliveryNote($page, $order)
    {
        $this->y += 7.5;

        $noteId = $order->getDeliveryNoteId();
        if($noteId)
        {
            $note = Mage::getModel('deliverynote/note')->load($noteId)->getNote();
            //split the note into lines with 120 characters per line
            $noteLine = Mage::helper('core/string')->str_split($note, 120, true, true);
            $numLine = count($noteLine);

            $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
            $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
            $page->setLineWidth(0.5);
            //draw rectangle the size of the message
            //draw delivery note rectangle (start x, start y, end x, end y co-ordinates)
            $page->drawRectangle(25, $this->y, 95, ($this->y -(10 * $numLine) -5));
            //white
            $page->setFillColor(new Zend_Pdf_Color_RGB(255, 255, 255));
            //draw the note rectangle
            $page->drawRectangle(95, $this->y, 570, ($this->y -(10 * $numLine) -5));
            //black
            $page->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));

            $this->y -= 10;
            $this->_setFontBold($page, 10);
            $page->drawText('Delivery Note:', 30, $this->y, 'UTF-8');
            $this->_setFontRegular($page, 10);

            foreach ($noteLine as $line)
            {
                $page->drawText($line, 100, $this->y, 'UTF-8');
                $this->y -= 10;
            }
        }
        else
        {
            $page->setFillColor(new Zend_Pdf_Color_RGB(0.93, 0.92, 0.92));
            $page->setLineColor(new Zend_Pdf_Color_GrayScale(0.5));
            $page->setLineWidth(0.5);
            //draw delivery note rectangle (start x, start y, end x, end y co-ordinates)
            $page->drawRectangle(25, $this->y, 95, $this->y -15);
            //white
            $page->setFillColor(new Zend_Pdf_Color_RGB(255, 255, 255));
            //draw the note rectangle
            $page->drawRectangle(95, $this->y, 570, $this->y -15);
            //black
            $page->setFillColor(new Zend_Pdf_Color_RGB(0, 0, 0));

            $this->y -= 10;
            $this->_setFontBold($page, 10);
            $page->drawText('Delivery Note:', 30, $this->y, 'UTF-8');
            $this->_setFontRegular($page, 10);
        }
        $this->y -= 12.5;
    }

}
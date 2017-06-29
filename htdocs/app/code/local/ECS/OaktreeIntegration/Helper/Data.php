<?php

class ECS_OaktreeIntegration_Helper_Data extends Mage_Core_Helper_Abstract
{
    public function getWSDLSoapConnectionNoHeader($url) {
        $soapConnection = new SoapClient($url, array(
            'trace' => 1
        ));
        return $soapConnection;
    }

    public function getWSDLSoapConnection($url, $namespace) {
        $soapConnection = $this->getWSDLSoapConnectionNoHeader($url);

        $header = new SoapHeader($namespace, "AuthHeader",
            (object) array(
                'UserName' => Mage::getStoreConfig('oaktreeintegration/auth/username'),
                'PassWord' => Mage::getStoreConfig('oaktreeintegration/auth/password')
            ));
        $soapConnection->__setSoapHeaders($header);
        return $soapConnection;
    }

    public function getSoapConnection($url, $namespace) {
        $soapConnection = new SoapClient(null, array(
            'trace' => 1,
            'location' => $url,
            'uri' => $namespace,
            'cache_wsdl' => WSDL_CACHE_NONE,
            'connection_timeout' => 1000
        ));

        $header = new SoapHeader($namespace, "AuthHeader",
            (object) array(
                new SoapVar(Mage::getStoreConfig("oaktreeintegration/auth/username"),XSD_STRING,null,null,'UserName',$namespace),
                new SoapVar(Mage::getStoreConfig("oaktreeintegration/auth/password"),XSD_STRING,null,null,'PassWord',$namespace),
            ));
        $soapConnection->__setSoapHeaders($header);
        return $soapConnection;
    }
}
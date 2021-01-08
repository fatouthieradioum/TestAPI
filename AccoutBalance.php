<?php

require 'SIB.php';
$sib= new SIB();
$solde= $sib->GetBalanceAccount();
$solde1= $sib->GetBalanceMini();
$solde2= $sib->GetAccountToAccountTransfer();

var_dump($solde2);die;


/*$url = 'http://fcubs.ofss.com/service/FCUBSAccService'.$merchant_address_url."&destination=".$customer_address_url."&sensor=false";
$response_xml_data = file_get_contents($url);
if($response_xml_data){
    echo "read";
}

$data = simplexml_load_string($response_xml_data);
echo "<pre>"; print_r($data); exit;*/


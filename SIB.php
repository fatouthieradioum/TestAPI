<?php
/**
 * Created by PhpStorm.
 * User: developpeur3
 * Date: 08/06/2016
 * Time: 15:36
 */
ini_set('display_errors', 1);
require_once('lib/nusoap.php');
class SIB
{

    private $soapUrl;
    private $soapUserName;
    private $soapPassword;
    private $soapAction;
    private $agence;
    private $caisse;
    public function __construct()
    {
        //$this->soapUrl = 'https://nsicmobile.senelec.sn:4800/XISOAPAdapter/MessageServlet?senderParty=&senderService=Poste&receiverParty=&receiverService=&interface=Payment&interfaceNamespace=https://senelec.com/SAP_ISU/Payment/v1';
        $this->soapUrl = 'http://10.2.0.9:7018/FCUBSAccService/FCUBSAccService';
        //$this->soapUserName = 'postetopi';
        //$this->soapPassword = '3YCJ&&k$';
        $this->soapAction = '';
        $this->agence = 'SENELECPX000003';
        $this->caisse = 'SENELECPX000001';
    }


public function xmlstr_to_array($xmlstr)
    {
        $doc = new DOMDocument();
        @$doc->loadXML($xmlstr);
        return $this->domnode_to_array($doc->documentElement);
    }
    public function domnode_to_array($node)
    {
        $output = array();
        switch ($node->nodeType) {
            case XML_CDATA_SECTION_NODE:
            case XML_TEXT_NODE:
                $output = trim($node->textContent);
                break;
            case XML_ELEMENT_NODE:
                for ($i=0, $m=$node->childNodes->length; $i<$m; $i++) {
                    $child = $node->childNodes->item($i);
                    $v = $this->domnode_to_array($child);
                    if(isset($child->tagName)) {
                        $t = $child->tagName;
                        if(!isset($output[$t])) {
                            $output[$t] = array();
                        }
                        $output[$t][] = $v;
                    }
                    elseif($v) {
                        $output = (string) $v;
                    }
                }
                if(is_array($output)) {
                    if($node->attributes->length) {
                        $a = array();
                        foreach($node->attributes as $attrName => $attrNode) {
                            $a[$attrName] = (string) $attrNode->value;
                        }
                        $output['@attributes'] = $a;
                    }
                    foreach ($output as $t => $v) {
                        if(is_array($v) && count($v)==1 && $t!='@attributes') {
                            $output[$t] = $v[0];
                        }
                    }
                }
                break;
        }
        return $output;
    }


    public function GetBalanceAccount(){
        // xml post structure
       
        $xml_post_string = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:fcub="http://fcubs.ofss.com/service/FCUBSAccService">
   <soapenv:Header/>
   <soapenv:Body>
      <fcub:QUERYACCTBAL_IOFS_REQ>
         <fcub:FCUBS_HEADER>
            <fcub:SOURCE>FCAT</fcub:SOURCE>
            <fcub:UBSCOMP>FCUBS</fcub:UBSCOMP>
            <!--Optional:-->
            <fcub:MSGID></fcub:MSGID>
            <!--Optional:-->
            <fcub:CORRELID></fcub:CORRELID>
            <fcub:USERID>FCATOP</fcub:USERID>
            <!--Optional:-->
            <fcub:ENTITY></fcub:ENTITY>
            <fcub:BRANCH>001</fcub:BRANCH>
            <!--Optional:-->
            <fcub:MODULEID>ST</fcub:MODULEID>
            <fcub:SERVICE>FCUBSAccService</fcub:SERVICE>
            <fcub:OPERATION>QueryAcctBal</fcub:OPERATION>
            <!--Optional:-->
            <fcub:SOURCE_OPERATION></fcub:SOURCE_OPERATION>
            <!--Optional:-->
            <fcub:SOURCE_USERID></fcub:SOURCE_USERID>
            <!--Optional:-->
            <fcub:DESTINATION></fcub:DESTINATION>
            <!--Optional:-->
            <fcub:MULTITRIPID></fcub:MULTITRIPID>
            <!--Optional:-->
            <fcub:FUNCTIONID>STQCUSBL</fcub:FUNCTIONID>
            <!--Optional:-->
            <fcub:ACTION>EXECUTEQUERY</fcub:ACTION>
            <!--Optional:-->
            <fcub:MSGSTAT></fcub:MSGSTAT>
            <!--Optional:-->
            <fcub:SNAPSHOTID></fcub:SNAPSHOTID>
            <!--Optional:-->
            <fcub:PASSWORD></fcub:PASSWORD>
            <!--Optional:-->
            <fcub:ADDL>
               <!--Zero or more repetitions:-->
               <fcub:PARAM>
                  <fcub:NAME></fcub:NAME>
                  <fcub:VALUE></fcub:VALUE>
               </fcub:PARAM>
            </fcub:ADDL>
         </fcub:FCUBS_HEADER>
         <fcub:FCUBS_BODY>
            <fcub:Custbal-IO>
               <fcub:CUST_AC_NO>001000229010</fcub:CUST_AC_NO>
               <fcub:BRHCODE>001</fcub:BRHCODE>
            </fcub:Custbal-IO>
         </fcub:FCUBS_BODY>
      </fcub:QUERYACCTBAL_IOFS_REQ>
   </soapenv:Body>
</soapenv:Envelope>';

//htmlentities(
        
        $headers = array(
            'Content-type: text/xml;charset="utf-8"',
            'Accept: text/xml',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
            'Content-length: '.strlen($xml_post_string),
        ); //SOAPAction: your op URL

        $url = $this->soapUrl;

        // PHP cURL  for https connection with auth
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       // curl_setopt($ch, CURLOPT_USERPWD, $this->soapUserName.':'.$this->soapPassword); // username and password - declared at the top of the doc
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // converting
        $response = curl_exec($ch);
        $error = curl_error($ch);
        if(strlen($error) === 0){
            return $response;
            //$xml =  str_replace('SOAP:','',$response);
            $json = json_encode($this->xmlstr_to_array($xml));
            return $json;
        }
        else{
        	return $error;
        }


    }
    public function GetBalanceMini(){
        // xml post structure

        $xml_post_string = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:fcub="http://fcubs.ofss.com/service/FCUBSACService">
   <soapenv:Header/>
   <soapenv:Body>
      <fcub:QUERYACCTRNS_IOFS_REQ>
         <fcub:FCUBS_HEADER>
            <fcub:SOURCE>FCAT</fcub:SOURCE>
            <fcub:UBSCOMP>FCUBS</fcub:UBSCOMP>
            <!--Optional:-->
            <fcub:MSGID></fcub:MSGID>
            <!--Optional:-->
            <fcub:CORRELID></fcub:CORRELID>
            <fcub:USERID>FCATOP</fcub:USERID>
            <!--Optional:-->
            <fcub:ENTITY></fcub:ENTITY>
            <fcub:BRANCH>001</fcub:BRANCH>
            <!--Optional:-->
            <fcub:MODULEID></fcub:MODULEID>
            <fcub:SERVICE>FCUBSACService</fcub:SERVICE>
            <fcub:OPERATION>QueryAccTrns</fcub:OPERATION>
            <!--Optional:-->
            <fcub:SOURCE_OPERATION></fcub:SOURCE_OPERATION>
            <!--Optional:-->
            <fcub:SOURCE_USERID></fcub:SOURCE_USERID>
            <!--Optional:-->
            <fcub:DESTINATION></fcub:DESTINATION>
            <!--Optional:-->
            <fcub:MULTITRIPID></fcub:MULTITRIPID>
            <!--Optional:-->
            <fcub:FUNCTIONID>ACQACTRN</fcub:FUNCTIONID>
            <!--Optional:-->
            <fcub:ACTION>VIEW</fcub:ACTION>
            <!--Optional:-->
            <fcub:MSGSTAT></fcub:MSGSTAT>
            <!--Optional:-->
            <fcub:SNAPSHOTID></fcub:SNAPSHOTID>
            <!--Optional:-->
            <fcub:PASSWORD></fcub:PASSWORD>
            <!--Optional:-->
            <fcub:ADDL>
               <!--Zero or more repetitions:-->
               <fcub:PARAM>
                  <fcub:NAME></fcub:NAME>
                  <fcub:VALUE></fcub:VALUE>
               </fcub:PARAM>
            </fcub:ADDL>
         </fcub:FCUBS_HEADER>
         <fcub:FCUBS_BODY>
            <fcub:Acc-Details-IO>
               <!--Optional:-->
               <fcub:NUMOFTRN>5</fcub:NUMOFTRN>
               <fcub:ACCNO>001000403049</fcub:ACCNO>
               <!--Optional:-->
               <fcub:ACCBRN>001</fcub:ACCBRN>
            </fcub:Acc-Details-IO>
         </fcub:FCUBS_BODY>
      </fcub:QUERYACCTRNS_IOFS_REQ>
   </soapenv:Body>
</soapenv:Envelope>';

//htmlentities(

        $headers = array(
            'Content-type: text/xml;charset="utf-8"',
            'Accept: text/xml',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
            'Content-length: '.strlen($xml_post_string),
        ); //SOAPAction: your op URL

        $url = $this->soapUrl;

        // PHP cURL  for https connection with auth
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       // curl_setopt($ch, CURLOPT_USERPWD, $this->soapUserName.':'.$this->soapPassword); // username and password - declared at the top of the doc
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // converting
        $response = curl_exec($ch);
        $error = curl_error($ch);
        if(strlen($error) === 0){
            return $response;
            //$xml =  str_replace('SOAP:','',$response);
            $json = json_encode($this->xmlstr_to_array($xml));
            return $json;
        }
        else{
        	return $error;
        }


    }
    public function GetAccountToAccountTransfer(){
        // xml post structure

        $xml_post_string = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:fcub="http://fcubs.ofss.com/service/FCUBSRTService">
        <soapenv:Header/>
        <soapenv:Body>
        <fcub:CREATETRANSACTION_FSFS_REQ>
        <fcub:FCUBS_HEADER>
        <fcub:SOURCE>FCAT</fcub:SOURCE>
        <fcub:UBSCOMP>FCUBS</fcub:UBSCOMP>
        <fcub:USERID>FCATOP</fcub:USERID>
        <fcub:BRANCH>001</fcub:BRANCH>
        <fcub:SERVICE>FCUBSRTService</fcub:SERVICE>
        <fcub:OPERATION>CreateTransaction</fcub:OPERATION>
        <fcub:ACTION>NEW</fcub:ACTION>
        </fcub:FCUBS_HEADER>
        <fcub:FCUBS_BODY>
        <fcub:Transaction-Details>
        <fcub:PRD>FTRQ</fcub:PRD>
        <fcub:BRN>001</fcub:BRN>
        <!--Optional:-->
        <fcub:MODULE>RT</fcub:MODULE>
        <!--Optional:-->
        <fcub:TXNBRN>220</fcub:TXNBRN>
        <!--Optional:-->
        <fcub:TXNACC>000072031501</fcub:TXNACC>
        <!--Optional:-->
        <fcub:TXNCCY>XOF</fcub:TXNCCY>
        <!--Optional:-->
        <fcub:TXNAMT>19000</fcub:TXNAMT>
        <fcub:OFFSETBRN>001</fcub:OFFSETBRN>
        <!--Optional:-->
        <fcub:OFFSETACC>000004801803</fcub:OFFSETACC>
        <!--Optional:-->
        <fcub:OFFSETCCY>XOF</fcub:OFFSETCCY>
        <!--Optional:-->
        <fcub:OFFSETAMT>19000</fcub:OFFSETAMT>
        <!--Optional:-->
        <fcub:OFFSETTRN>19000</fcub:OFFSETTRN>
        <!--Optional:-->
        <fcub:XRATE>1</fcub:XRATE>
        <!--Optional:-->
        <fcub:LCYAMT>19000</fcub:LCYAMT>
        </fcub:Transaction-Details>
        </fcub:FCUBS_BODY>
        </fcub:CREATETRANSACTION_FSFS_REQ>
        </soapenv:Body>
        </soapenv:Envelope>';

//htmlentities(

        $headers = array(
            'Content-type: text/xml;charset="utf-8"',
            'Accept: text/xml',
            'Cache-Control: no-cache',
            'Pragma: no-cache',
            'Content-length: '.strlen($xml_post_string),
        ); //SOAPAction: your op URL

        $url = $this->soapUrl;

        // PHP cURL  for https connection with auth
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       // curl_setopt($ch, CURLOPT_USERPWD, $this->soapUserName.':'.$this->soapPassword); // username and password - declared at the top of the doc
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // converting
        $response = curl_exec($ch);
        $error = curl_error($ch);
        if(strlen($error) === 0){
            return $response;
            //$xml =  str_replace('SOAP:','',$response);
            $json = json_encode($this->xmlstr_to_array($xml));
            return $json;
        }
        else{
        	return $error;
        }


    }




}
    
//$in = new SENELEC();
//echo $in->GetAgentDepositAmount();

?>
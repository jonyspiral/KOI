<?php require_once('../premaster.php'); ?>
<?php
# Author: Gerardo Fisanotti - DvSHyS/DiOPIN/AFIP - 21-apr-07
# Function: Get info about a CUIT as provided by WS Consulta PUC
# Input:
#        TA.XML provided by WSAA (see wsaa-client)
#        CUIT to search PUC for
#
# Output:
#        CONTRIBUYENTE.XML as provided by WSPN3
#==============================================================================
define ("WSDL", "wspn3.wsdl"); # The WSDL
define ("TA", "TA.xml");     # TA.XML as provided by WSAA
# WSPN3URL: the URL to access WSPN3, check for http or https 
#define ("WSPN3URL", "https://setiwsh2/padron-puc-ws/" .
#define ("WSPN3URL", "https://setipagohomo.afip.gov.ar/padron-puc-ws/" .
define ("WSPN3URL", "https://awshomo.afip.gov.ar/padron-puc-ws/" .
                    "services/select.ContribuyenteNivel3SelectServiceImpl");
# CUIT: the CUIT to search for
#define ("CUIT", "30671513963");
#define ("CUIT", "20079519783");
define ("CUIT", "20055814121");
# You shouldn't have to change anything below this line!!!
#==============================================================================
function CreateContribuyente($CUIT)
{
  $XML = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?>' .
                              '<contribuyentePK></contribuyentePK>');
  $XML->addChild('id',$CUIT);
  return $XML->asXML();
}
#==============================================================================
function CreateToken($token)
{
  $xmlTOKEN="-----BEGIN SSOTOKENBASE64-----\n" .
         $token .
         "\n-----END SSOTOKENBASE64-----\n";
  return $xmlTOKEN;
}
#==============================================================================
function CreateSign($sign)
{
  $xmlSIGN= "-----BEGIN SSOSIGNBASE64-----\n" .
         $sign .
         "\n-----END SSOSIGNBASE64-----\n";
  return $xmlSIGN;
}
#==============================================================================
if (!file_exists(WSDL)) {exit("Failed to open ".WSDL."\n");}
if (!file_exists(TA)) {exit("Failed to open ".TA."\n");}
$xml=simplexml_load_file(TA);
$client=new SoapClient(WSDL,
  array(
        'soap_version'=> SOAP_1_1,  # It seems that WSPN3 is 1_1 only!
        'location'    => WSPN3URL,
        'trace'       => 1, # needed by getLastRequestHeaders and others
        'proxy_host'  => "proxy",
        'proxy_port'  => 80,
        'exceptions'  => 0));
//$fe = new FacturaElectronica();
//$array = $fe->autenticar();
$results=$client->get(CreateContribuyente(CUIT),
                      CreateToken($array['token']),
                      CreateSign($array['sign']));
if (is_soap_fault($results)) 
  { trigger_error("Fault: {$results->faultcode} {$results->faultstring}",
                  E_USER_ERROR); 
  }
#printf ("HEADERs:\n%s\n", $client->__getLastRequestHeaders());
file_put_contents("request.xml", $client->__getLastRequest());
file_put_contents("response.xml", $client->__getLastResponse());
$xml=simplexml_load_string((string)$results);
file_put_contents("CONTRIBUYENTE.xml",$xml->asXML());
#echo $xml->persona->documento;
#echo $xml->persona->{'descripcion-corta'};
?>

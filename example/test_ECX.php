<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set('memory_limit', '256M');
set_time_limit(300000);
require_once ('class.XMPLPI.php');
$xmlpi = new XMLPI('test'); //mode:   test/live
#please write dedicated credentials for TEST and LIVE envirnoment in XMPLPI class !  ($credentials array)
function create_data()
{
    $data['ShipperAccountNumber'] = '************'; //Shipper/ Payer DHL Account
    $data['Consignee'] = array( //Receiver data
        'CompanyName' => 'Jan Kowalski Company',
        'AddressLine1' => 'berliner str1', 
        'AddressLine2' => ' str1', 
        'AddressLine3' => '', 
        'City' => 'Hamburg',
        'PostalCode' => '20068',
        'CountryCode' => 'DE',
        'CountryName' => 'GERMANY',
        'Contact' => array(
            'PersonName' => 'Jan Kowalskie',
            'PhoneNumber' => 'q34553',
            'PhoneExtension' => '',
            'Telex' => '',
            'Email' => 'test_receiver@o2.pl',
            
        )
    );
    $data['Reference'] = array(
        'ReferenceID' => 'Shipment reference'
    );
    $data['IsDutiable'] = 'N'; //dutiable shipment ('Y'/'N');  Y for GlobalProductCode P,H,Y,E,M
    $data['ShipmentDetails'] = array( //details of shipment
        'Pieces' => array(
            'Piece' => array(
                'PackageType' => 'CP',
                'Weight' => '1.20',
                'Width' => '1',
                'Height' => '1',
                'Depth' => '1'
            ) ,
            'Piece' => array(
                'PackageType' => 'CP',
                'Weight' => '2.20',
                'Width' => '12',
                'Height' => '13',
                'Depth' => '14'
            )
            //,'Piece'=>array('PackageType'=>'CP','Weight'=>'1.20','Width'=>'1','Height'=>'1','Depth'=>'1')
            
        ) ,
        'WeightUnit' => 'K',
        'GlobalProductCode' => 'U', //product one from P,U,D,T,K,Y,M,H,W....   
        'Date' => date("Y-m-d") ,
        'Contents' => 'Content', //Content
        'DimensionUnit' => 'C',
        'IsDutiable' => $data['IsDutiable'],
        'CurrencyCode' => 'PLN', //currency
        'CustData' => 'dodatkowe informacje', //Additional information
        
    );
    $data['Shipper'] = array(
        'ShipperID' => $data['ShipperAccountNumber'],
        'CompanyName' => 'Send COmpany',
        'AddressLine1' => 'Stawowa 113m', 
        'AddressLine2' => '',
        'AddressLine3' => '',
        'City' => 'Katy Wroclawskie',
        'PostalCode' => '55-080',
        'CountryCode' => 'PL',
        'CountryName' => 'Poland',
        'Contact' => array(
            'PersonName' => 'Jan Kowalski',
            'PhoneNumber' => '487100000',
            'PhoneExtension' => '',
            'Telex' => '',
            'Email' => 'contact@test.com',
        )
    );

    $data['LabelImageFormat'] = 'PDF'; // PDF / ZPL2
    $data['RequestArchiveDoc'] = 'Y'; //generate "WaybillDOC" 
    $data['Label'] = array(
        'LabelTemplate' => '8X4_PDF'
    ); //schema of label - / 6X4_PDF,a 8X4_thermal, 6X4_thermal - thermal only for ZPL2
    #additionaly services
    //$data['SpecialService'][]=array('SpecialServiceType'		=>'AA',)
    $data['EProcShip'] = 'N'; // Y -without AWB - check only,  N - with AWB
    return $data;
}

#1 Step - prepare input data
$xmlpi->create_xml_out(create_data());

#2 Step  - send request
$xmlpi->send_request_XML();

#3 Step - check response
if ((string)($xmlpi
    ->xml_in_SimpleXML
    ->Note
    ->ActionNote) == Success)
{
    //OK - Success
    $label = base64_decode($xmlpi
        ->xml_in_SimpleXML
        ->LabelImage
        ->OutputImage);
    file_put_contents($xmlpi
        ->xml_in_SimpleXML->AirwayBillNumber . '.' . $xmlpi
        ->xml_in_SimpleXML
        ->LabelImage->OutputFormat, $label);
    echo '<hr>Label saved: ' . $xmlpi
        ->xml_in_SimpleXML->AirwayBillNumber . '.' . $xmlpi
        ->xml_in_SimpleXML
        ->LabelImage->OutputFormat . '<hr>';
}
else
{
    //ERRROR -show errors
    XMLPI::tab($xmlpi
        ->xml_in_SimpleXML
        ->Response
        ->Status);
}

//XMLPI::tab($xmlpi->xml_out);
?>

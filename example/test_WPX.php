<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set('memory_limit', '256M');
set_time_limit(300000);
require_once ('class.XMPLPI.php');
$xmlpi = new XMLPI('test'); //#Tryb test/live
#please write dedicated credentials for TEST and LIVE envirnoment in XMPLPI class !  ($credentials array)
function create_data()
{
    $data['ShipperAccountNumber'] = '********'; //Shipper/ Payer DHL Account
    $data['Consignee'] = array( //Receiver data
        'CompanyName' => 'Jan Kowalski Company',
        'AddressLine1' => 'berliner str1', 
        'AddressLine2' => ' str1',
        'AddressLine3' => '',
        'City' => 'Basel',
        'PostalCode' => '9000',
        'CountryCode' => 'CH',
        'CountryName' => 'Switzerland',
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
    $data['IsDutiable'] = 'Y'; //dutiable shipment ('Y'/'N');  Y for GlobalProductCode P,H,Y,E,M
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
        'GlobalProductCode' => 'P', //product one from P,U,D,T,K,Y,M,H,W....   
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
    ##########      ONLY FOR DUTIES SHIPMENTS	################################################################################################################
    //only if $data['IsDutiable']='Y'
    $data['ExportDeclaration'] = array(
        'SignatureName' => 'afsdgasfd', //O
        'ExportReason' => 'Sale', //O
        'ExportReasonCode' => 'P', //P (Permanent)T ( Temporary)R ( Re-Export)
        'InvoiceNumber' => 'zdfgsdf', //numer of invoice
        'InvoiceDate' => date("Y-m-d") ,
        'ExportLineItem' => array(
            array(
                'LineNumber' => '1',
                'Quantity' => '13',
                'QuantityUnit' => 'PCS',
                'Description' => 'dfsasdfasdf',
                'Value' => '34.45', //for all pieces - for item of infoice
                'CommodityCode' => '3456345',
                'Weight' => array(
                    'Weight' => '1.0',
                    'WeightUnit' => 'K'
                ) ,
                'GrossWeight' => array(
                    'Weight' => '1.0',
                    'WeightUnit' => 'K'
                ) ,
                'ManufactureCountryCode' => 'PL'
            ) ,
            array(
                'LineNumber' => '2',
                'Quantity' => '3',
                'QuantityUnit' => 'PCS',
                'Description' => 'cos tam',
                'Value' => '22.45', //for all pieces - for item of infoice
                'CommodityCode' => '3456345',
                'Weight' => array(
                    'Weight' => '2.4',
                    'WeightUnit' => 'K'
                ) ,
                'GrossWeight' => array(
                    'Weight' => '2.4',
                    'WeightUnit' => 'K'
                ) ,
                'ManufactureCountryCode' => 'PL'
            )
        ) ,
        'PlaceOfIncoterm' => 'Katy wroclawskie'
        //Place of incoterms
        
    );
    //upload of own invoice
    $data['DocImages'] = array(
        'DocImage' => array(
            'Type' => 'CIN', //typ faktury (CIN _commercial invoice)
            'Image' => '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IDAgMCAwIDYwMCAwIDAgMAo2MDAgMCAwIDYwMCA2MDBdCi9FbmNvZGluZy9XaW5BbnNpRW5jb2RpbmcvU3VidHlwZS9UeXBlMT4+CmVuZG9iago4IDAgb2JqCjw8L1R5cGUvRm9udERlc2NyaXB0b3IvRm9udE5hbWUvT0JYSVVTK0NvdXJpZXIvRm9udEJCb3hbMCAtMTYgNTM1IDU3Nl0vRmxhZ3MgNQovQXNjZW50IDU3NgovQ2FwSGVpZ2h0IDU3NgovRGVzY2VudCAtMTYKL0l0YWxpY0FuZ2xlIDAKL1N0ZW1WIDgwCi9BdmdXaWR0aCA2MDAKL01heFdpZHRoIDYwMAovTWlzc2luZ1dpZHRoIDYwMAovQ2hhclNldCgvTC9vbmUvUC9FL1MvVC9zcGFjZSkvRm9udEZpbGUzIDEyIDAgUj4+CmVuZG9iagoyIDAgb2JqCjw8L1Byb2R1Y2VyKEdQTCBHaG9zdHNjcmlwdCA4LjE1KQovQ3JlYXRpb25EYXRlKEQ6MjAyMDA1MjcxNTA4NTMpCi9Nb2REYXRlKEQ6MjAyMDA1MjcxNTA4NTMpCi9UaXRsZShuZXcgMSkKL0NyZWF0b3IoUFNjcmlwdDUuZGxsIFZlcnNpb24gNS4yLjIpCi9BdXRob3IobWtvbGFrKT4+ZW5kb2JqCnhyZWYKMCAxNAowMDAwMDAwMDAwIDY1NTM1IGYgCjAwMDAwMDA1MjcgMDAwMDAgbiAKMDAwMDAwMjEzOCAwMDAwMCBuIAowMDAwMDAwNDU5IDAwMDAwIG4gCjAwMDAwMDAyOTkgMDAwMDAgbiAKMDAwMDAwMDAxNSAwMDAwMCBuIAowMDAwMDAwMjgwIDAwMDAwIG4gCjAwMDAwMDA1NzUgMDAwMDAgbiAKMDAwMDAwMTg4MyAwMDAwMCBuIAowMDAwMDAxNjEzIDAwMDAwIG4gCjAwMDAwMDA2MTYgMDAwMDAgbiAKMDAwMDAwMDY0NiAwMDAwMCBuIAowMDAwMDAwNjc2IDAwMDAwIG4gCjAwMDAwMDE1OTMgMDAwMDAgbiAKdHJhaWxlcgo8PCAvU2l6ZSAxNCAvUm9vdCAxIDAgUiAvSW5mbyAyIDAgUgovSUQgWyh8jAba95hzmAIlia50X4QCKSh8jAba95hzmAIlia50X4QCKV0KPj4Kc3RhcnR4cmVmCjIzMTQKJSVFT0YK', //tu wstawiamy zakodowany w base64 obraz faktury
            'ImageFormat' => 'PDF'          
        )
    );

    /////////
    $data['LabelImageFormat'] = 'PDF'; // PDF / ZPL2
    $data['RequestArchiveDoc'] = 'Y';//generate "WaybillDOC" 
    $data['Label'] = array(
        'LabelTemplate' => '8X4_PDF'
    ); //schema of label - / 6X4_PDF,a 8X4_thermal, 6X4_thermal - thermal only for ZPL2
    #additionaly services
    $data['SpecialService'][] = array(
        'SpecialServiceType' => 'WY',
    ); /*PLT Service */
    
    //$data['SpecialService'][]=array('SpecialServiceType'		=>'DD',); //DTP/DDP - nalezy pamietać o incoterms
    $data['EProcShip'] = 'N'; // Y -without AWB - check only,  N - with AWB
    
    $data['Dutiable'] = array(
        'DeclaredValue' => '22.56', //format xx.xx
        'DeclaredCurrency' => 'EUR',
        'TermsOfTrade' => 'DAP'  //incoterms  DAP/ DDP
        
    );
    return $data;
}

#1 Step - prepare input data
$xmlpi->create_xml_out(create_data()); //generowanie XML_out który zostanie wysłany do WebService

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
    echo '<hr>List zapisany: ' . $xmlpi
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

<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set('memory_limit', '256M');
set_time_limit (300000);
require_once('class.XMPLPI.php');
$xmlpi=new XMLPI('test'); //#Tryb test/live 
#proszę wpisać w klasę XMLPI dane dostepowe - w $credentials

# parametry przesyłki - create_data() # - prosze je ewentualnie modyfikować
function create_data(){
	$data['ShipperAccountNumber']='*******';//DHL Account number
	$data['Consignee']=array( 					//Dane odbiorcy
							'CompanyName'	=>'Jan Kowalski Company',
							'AddressLine1'	=>'berliner str1', 	//wymagane
							'AddressLine2'	=>' str1', 			//O
							'AddressLine3'	=>'', 				//O
							'City'			=>'Hamburg',
							'PostalCode'	=>'20068',
							'CountryCode'	=>'DE',
							'CountryName'	=>'GERMANY',
							'Contact'		=>array(
													'PersonName'		=>'Jan Kowalskie',
													'PhoneNumber'		=>'q34553',
													'PhoneExtension'	=>'',//O
													'Telex'				=>'',//O
													'Email'				=>'test_receiver@o2.pl',//O
													)
							);
	$data['Reference']=array('ReferenceID'=>'Referencja przesyłki'); //referencje przesyłki - znajdują się na fakturze z DHL
	$data['IsDutiable']='N';										//czy produkt celny ('Y'/'N'); znacznik Y powinien być uzyty dla produktów P,H,Y,E,M
	$data['ShipmentDetails']=array(		//szczegóły przesylki
									'Pieces'			=>array(
														'Piece'=>array('PackageType'=>'CP','Weight'=>'1.20','Width'=>'1','Height'=>'1','Depth'=>'1')
														,'Piece'=>array('PackageType'=>'CP','Weight'=>'2.20','Width'=>'12','Height'=>'13','Depth'=>'14')
														//,'Piece'=>array('PackageType'=>'CP','Weight'=>'1.20','Width'=>'1','Height'=>'1','Depth'=>'1')
																),
									'WeightUnit'		=>'K',
									'GlobalProductCode'	=>'U', //produkt" P,U,D,T,K,Y,M,H,W....   P- przesyłka celna poza EU, U- przesyłka niecelna do EU
									'Date'				=>date("Y-m-d"),
									'Contents'			=>'Zawartosc', //zawartosc przesylki
									'DimensionUnit'		=>'C',
									'IsDutiable'		=>$data['IsDutiable'],
									'CurrencyCode'		=>'PLN', //waluta - zostawiamy PLN
									'CustData'			=>'dodatkowe informacje', //dodatkowe informacje drukowany pomiedzy 2 a 3 kodem paskowycm
	);
	$data['Shipper']=array(
							'ShipperID'		=>$data['ShipperAccountNumber'],
							'CompanyName'	=>'Send COmpany',
							'AddressLine1'	=>'Stawowa 113m',//wymagane tylko addresline 1
							'AddressLine2'	=>'',
							'AddressLine3'	=>'',
							'City'			=>'Katy Wroclawskie',
							'PostalCode'	=>'55-080',
							'CountryCode'	=>'PL',
							'CountryName'	=>'Poland',
							'Contact'		=>array(
													'PersonName'		=>'Jan Kowalski',
													'PhoneNumber'		=>'487100000',
													'PhoneExtension'	=>'',
													'Telex'				=>'',
													'Email'				=>'contact@test.com',
													)
							);	
##########      TYLKO dla przeysłek CELNYCH	################################################################################################################
	//czyli jeśli $data['IsDutiable']='Y' - w innym przypadku poniższe tablice nie są używane
	$data['ExportDeclaration']=array(
								'SignatureName'			=>'afsdgasfd',//O
								'ExportReason'			=>'Sale',	//O
								'ExportReasonCode'		=>'P',		//P (Permanent)T ( Temporary)R ( Re-Export) 
								'InvoiceNumber'			=>'zdfgsdf',//numer faktury
								'InvoiceDate'			=>date("Y-m-d"),
								'ExportLineItem'		=>array(
															array(
																'LineNumber'			=>'1',
																'Quantity'				=>'13',
																'QuantityUnit'			=>'PCS',
																'Description'			=>'dfsasdfasdf',
																'Value'					=>'34.45', //for all pieces - for item of infoice
																'CommodityCode'			=>'3456345',
																'Weight'				=>array(
																							'Weight'		=>'1.0',
																							'WeightUnit'	=>'K'
																						),
																'GrossWeight'			=>array(
																							'Weight'		=>'1.0',
																							'WeightUnit'	=>'K'
																						),
																'ManufactureCountryCode'=>'PL'
															)
															,array(
																'LineNumber'			=>'2',
																'Quantity'				=>'3',
																'QuantityUnit'			=>'PCS',																
																'Description'			=>'cos tam',
																'Value'					=>'22.45', //for all pieces - for item of infoice
																'CommodityCode'			=>'3456345',
																'Weight'				=>array(
																							'Weight'		=>'2.4',
																							'WeightUnit'	=>'K'
																						),
																'GrossWeight'			=>array(
																							'Weight'		=>'2.4',
																							'WeightUnit'	=>'K'
																						),
																'ManufactureCountryCode'=>'PL'
															)
														),
									'PlaceOfIncoterm'	=>'Katy wroclawskie' //Place of incoterms

							);
	//tylko dla przeysłek celnych i tylko wtedy jeśli chcemy uploadować własną fakturę - jest to uzupełnienie usługi PLT
	
	$data['DocImages']=array(
							'DocImage'=>array(
											'Type'			=>'CIN', //typ faktury (CIN _commercial invoice)
											'Image'			=>'JVBERi0x.............o1MzUyCiUlRU9GCg==', //tu wstawiamy zakodowany w base64 obraz faktury
											'ImageFormat'	=>'PDF' //format faktury (np PDF)
											)
	);
	
	/////////
	$data['LabelImageFormat']='PDF'; //oczekiwany format listu przewozowego : PDF lub ZPL2
	$data['RequestArchiveDoc']='Y'; //generowanie listu: "WaybillDOC" - kopii listu przewozowego
	$data['Label']=array('LabelTemplate'=>'8X4_PDF'); //szablon listu - / 6X4_PDF, 8X4_thermal, 6X4_thermal - thermal wyłacznie dla ZPL2
	
	#usługi dodatkowe - jesli maja być
		$data['SpecialService'][]=array('SpecialServiceType'		=>'WY',); /*PLT - TYlko jesli 
													załaczamy elektroniczny obraz faktury (dla prodktów celny: P,Y,M,E,H)
													niektóre kraje nie akcpetują PLT lub akcpetują z limitem wartościowym: https://app.dhlexpress.pl/plt/inc/index.php
													*/
		//$data['SpecialService'][]=array('SpecialServiceType'		=>'DD',); //DTP/DDP - nalezy pamietać o incoterms
	$data['EProcShip']='N'; // Y -bez AWB,  N - z AWB . Y pozwala wysałać requesta do walidacji i nie otrzymamy do niego listu przewozowego

	//jesli celny wymagane, jesli nie celny można zignorować nie jest generowana ta sekcja w XML
	$data['Dutiable']=array(
							'DeclaredValue'		=>'22.56',	//zawsze w formacie xx.xx (zawsze czesci setne z rozdzielaczem w postaci kropki)
							'DeclaredCurrency'	=>'EUR',
							'TermsOfTrade'		=>'DAP'			//incoterms  DAP/ DDP
							);
	return $data;
}

#1 krok - Przygotowanie XML OUT - czyli Requesta na bazie danych z funkcji create_data()
	$xmlpi->create_xml_out(create_data()); //generowanie XML_out który zostanie wysłany do WebService
	#mamy gotowy Request w zmiennej $xmlpi->xml_out (+ do zmiennej   $xmlpi->xml_out_SimpleXML w formacie SimpleXML)
#2krok  - Wysłanie do WebService Requesta i wpisanie Response do zmiennej $xmlpi->xml_in  + do zmiennej   $xmlpi->xml_in_SimpleXML w formacie SimpleXML)
	$xmlpi->send_request_XML();
#3krok - sprawdzenie odpowiedzi
	if ((string)($xmlpi->xml_in_SimpleXML->Note->ActionNote)==Success){
		//OK - dekodowanie etykiety
		$label=base64_decode($xmlpi->xml_in_SimpleXML->LabelImage->OutputImage);
		file_put_contents($xmlpi->xml_in_SimpleXML->AirwayBillNumber.'.'.$xmlpi->xml_in_SimpleXML->LabelImage->OutputFormat,$label);
		echo '<hr>List zapisany: '.$xmlpi->xml_in_SimpleXML->AirwayBillNumber.'.'.$xmlpi->xml_in_SimpleXML->LabelImage->OutputFormat.'<hr>';
	}else{
		//ERRROR - wyświetlenie błędu
		XMLPI::tab($xmlpi->xml_in_SimpleXML->Response->Status);
	}

//XMLPI::tab($xmlpi->xml_out);
?>

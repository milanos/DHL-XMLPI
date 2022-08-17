<?php
error_reporting(E_ALL ^ E_NOTICE);
ini_set('memory_limit', '256M');
set_time_limit (300000);
require_once('class.XMPLPI.php');
$xmlpi=new XMLPI();
#proszę wpisać w klasę XMLPI dane dostepowe - w $credentials
#Tryb test/live ustawia się w klasie w zmiennej $mode=(ENUM('test','live'))

				# parametry przesyłki - create_data() # - prosze je ewentualnie modyfikować
function create_data(){
	$data['ShipperAccountNumber']='414606520';//numer konta płatnika/nadawcy
	$data['Consignee']=array( 					//Dane odbiorcy
							'CompanyName'	=>'Jan Kowalski Company',
							'AddressLine1'	=>'berliner str1', 	//wymagane
							'AddressLine2'	=>' str1', 			//opcja
							'AddressLine3'	=>'', 				//opcja
							'City'			=>'New York',
							'PostalCode'	=>'10001',
							'CountryCode'	=>'US',
							'CountryName'	=>'United States',
							'Contact'		=>array(
													'PersonName'		=>'Jan Kowalskie',
													'PhoneNumber'		=>'q34553',
													'PhoneExtension'	=>'',
													'Telex'				=>'',
													'Email'				=>'test_receiver@o2.pl',
													)
							);
	$data['Reference']=array('ReferenceID'=>'Referencja przesyłki'); //referencje przesyłki - znajdują się na fakturze z DHL
	$data['IsDutiable']='Y';										//czy produkt celny ('Y'/'N'); znacznik Y powinien być uzyty dla produktów P,H,Y,E,M
	$data['ShipmentDetails']=array(		//szczegóły przesylki
									//'NumberOfPieces'	=>'1', //znika z nowego schema a ilosc jest okreslana z PIECES
									'Pieces'			=>array(
														'Piece'=>array('PackageType'=>'CP','Weight'=>'1.20','Width'=>'1','Height'=>'1','Depth'=>'1')
														,'Piece'=>array('PackageType'=>'CP','Weight'=>'2.20','Width'=>'12','Height'=>'13','Depth'=>'14')
														//,'Piece'=>array('PackageType'=>'CP','Weight'=>'1.20','Width'=>'1','Height'=>'1','Depth'=>'1')
																),
									//'Weight'			=>'1.50',  //znika z nowego schema a ilosc jest okreslana z PIECES
									'WeightUnit'		=>'K',
									'GlobalProductCode'	=>'P', //produkt" P,U,D,T,K,Y,M,H,W....   P- przesyłka celna poza EU, U- przesyłka niecelna do EU
									'Date'				=>date("Y-m-d"),
									'Contents'			=>'Zawartosc', //zawartosc przesylki
									'DimensionUnit'		=>'C',
									'IsDutiable'		=>$data['IsDutiable'],
									'CurrencyCode'		=>'PLN', //waluta - zostawiamy PLN
									'CustData'			=>'dodatkowe informacje', //dodatkowe informacje drukowany pomiedzy 2 a 3 kodem paskowycm
	);
	//ExportDeclaration tylko dla przeysłek poza EU - czyli jeśli $data['IsDutiable']='Y' - w innym przypadku ta tablica nie jest brana pod uwagę
	$data['ExportDeclaration']=array(
								'SignatureName'			=>'afsdgasfd',
								'ExportReason'			=>'Sale',
								'ExportReasonCode'		=>'P',		//P (Permanent)T ( Temporary)R ( Re-Export) 
								'InvoiceNumber'			=>'zdfgsdf',
								'InvoiceDate'			=>date("Y-m-d"),
								'ExportLineItem'		=>array(
															array(
																'LineNumber'			=>'1',
																'Quantity'				=>'1',
																'QuantityUnit'			=>'PCS',
																'Description'			=>'dfsasdfasdf',
																'Value'					=>'34.45',
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
																'Value'					=>'22.45',
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
														)

							);
	$data['Shipper']=array(
							'ShipperID'		=>$data['ShipperAccountNumber'],
							'CompanyName'	=>'Keelog',
							'AddressLine1'	=>'Stawowa 11m',//wymagane tylko addresline 1
							'AddressLine2'	=>'',
							'AddressLine3'	=>'',
							'City'			=>'Domaszczyn',
							'PostalCode'	=>'55-095',
							'CountryCode'	=>'PL',
							'CountryName'	=>'Poland',
							'Contact'		=>array(
													'PersonName'		=>'Jaroslaw Paluszynski',
													'PhoneNumber'		=>'487100000',
													'PhoneExtension'	=>'',
													'Telex'				=>'',
													'Email'				=>'contact@keelog.com',
													)
							);
	//sekcja generowana tylko dla przeysłek celnych i tylko wtedy jeśli istnieje zakodowan base 64 faktura						
	$data['DocImages']=array(
							'DocImage'=>array(
											'Type'			=>'CIN', //typ faktury (CIN,
											'Image'			=>'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cGRmPSdodHRwOi8vbnMuYWRvYmUuY29tL3BkZi8xLjMvJz48cGRmOlByb2R1Y2VyPlBERkNyZWF0b3IgRnJlZSAzLjQuMTwvcGRmOlByb2R1Y2VyPgo8cGRmOktleXdvcmRzPjwvcGRmOktleXdvcmRzPgo8L3JkZjpEZXNjcmlwdGlvbj4KPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9J3V1aWQ6OWJjMmM3NzAtZGI3MS0xMWU5LTAwMDAtNDg5YTlkODk0NTJmJyB4bWxuczp4bXA9J2h0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC8nPjx4bXA6TW9kaWZ5RGF0ZT4yMDE5LTA5LTE3VDA4OjQwOjU5KzAyOjAwPC94bXA6TW9kaWZ5RGF0ZT4KPHhtcDpDcmVhdGVEYXRlPjIwMTktMDktMTdUMDg6NDA6NTkrMDI6MDA8L3htcDpDcmVhdGVEYXRlPgo8eG1wOkNyZWF0b3JUb29sPlBERkNyZWF0b3IgRnJlZSAzLjQuMTwveG1wOkNyZWF0b3JUb29sPjwvcmRmOkRlc2NyaXB0aW9uPgo8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0ndXVpZDo5YmMyYzc3MC1kYjcxLTExZTktMDAwMC00ODlhOWQ4OTQ1MmYnIHhtbG5zOnhhcE1NPSdodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvbW0vJyB4YXBNTTpEb2N1bWVudElEPSd1dWlkOjliYzJjNzcwLWRiNzEtMTFlOS0wMDAwLTQ4OWE5ZDg5NDUyZicvPgo8cmRmOkRlc2NyaXB0aW9uIHJkZjphYm91dD0ndXVpZDo5YmMyYzc3MC1kYjcxLTExZTktMDAwMC00ODlhOWQ4OTQ1MmYnIHhtbG5zOmRjPSdodHRwOi8vcHVybC5vcmcvZGMvZWxlbWVudHMvMS4xLycgZGM6Zm9ybWF0PSdhcHBsaWNhdGlvbi9wZGYnPjxkYzp0aXRsZT48cmRmOkFsdD48cmRmOmxpIHhtbDpsYW5nPSd4LWRlZmF1bHQnPmVtcHR5IHBhZ2U8L3JkZjpsaT48L3JkZjpBbHQ+PC9kYzp0aXRsZT48ZGM6Y3JlYXRvcj48cmRmOlNlcT48cmRmOmxpPmVtcHR5IHBhZ2U8L3JkZjpsaT48L3JkZjpTZXE+PC9kYzpjcmVhdG9yPjxkYzpkZXNjcmlwdGlvbj48cmRmOkFsdD48cmRmOmxpIHhtbDpsYW5nPSd4LWRlZmF1bHQnPjwvcmRmOmxpPjwvcmRmOkFsdD48L2RjOmRlc2NyaXB0aW9uPjwvcmRmOkRlc2NyaXB0aW9uPgo8L3JkZjpSREY+CjwveDp4bXBtZXRhPgogICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAKICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgICAgCjw/eHBhY2tldCBlbmQ9J3cnPz4KZW5kc3RyZWFtCmVuZG9iagoyIDAgb2JqCjw8L1Byb2R1Y2VyKFwzNzZcMzc3XDAwMFBcMDAwRFwwMDBGXDAwMENcMDAwclwwMDBlXDAwMGFcMDAwdFwwMDBvXDAwMHJcMDAwIFwwMDBGXDAwMHJcMDAwZVwwMDBlXDAwMCBcMDAwM1wwMDAuXDAwMDRcMDAwLlwwMDAxKQovQ3JlYXRpb25EYXRlKEQ6MjAxOTA5MTcwODQwNTkrMDInMDAnKQovTW9kRGF0ZShEOjIwMTkwOTE3MDg0MDU5KzAyJzAwJykKL1RpdGxlKFwzNzZcMzc3XDAwMGVcMDAwbVwwMDBwXDAwMHRcMDAweVwwMDAgXDAwMHBcMDAwYVwwMDBnXDAwMGUpCi9BdXRob3IoXDM3NlwzNzdcMDAwZVwwMDBtXDAwMHBcMDAwdFwwMDB5XDAwMCBcMDAwcFwwMDBhXDAwMGdcMDAwZSkKL1N1YmplY3QoXDM3NlwzNzcpCi9LZXl3b3JkcyhcMzc2XDM3NykKL0NyZWF0b3IoXDM3NlwzNzdcMDAwUFwwMDBEXDAwMEZcMDAwQ1wwMDByXDAwMGVcMDAwYVwwMDB0XDAwMG9cMDAwclwwMDAgXDAwMEZcMDAwclwwMDBlXDAwMGVcMDAwIFwwMDAzXDAwMC5cMDAwNFwwMDAuXDAwMDEpPj5lbmRvYmoKeHJlZgowIDE0CjAwMDAwMDAwMDAgNjU1MzUgZiAKMDAwMDAwMDU0NiAwMDAwMCBuIAowMDAwMDA0ODM4IDAwMDAwIG4gCjAwMDAwMDA0ODcgMDAwMDAgbiAKMDAwMDAwMDMyNyAwMDAwMCBuIAowMDAwMDAwMDE1IDAwMDAwIG4gCjAwMDAwMDAzMDggMDAwMDAgbiAKMDAwMDAwMDY5MiAwMDAwMCBuIAowMDAwMDAwNzkzIDAwMDAwIG4gCjAwMDAwMDExNTYgMDAwMDAgbiAKMDAwMDAwMDczMyAwMDAwMCBuIAowMDAwMDAwNzYzIDAwMDAwIG4gCjAwMDAwMDE0NjQgMDAwMDAgbiAKMDAwMDAwMzE4OSAwMDAwMCBuIAp0cmFpbGVyCjw8IC9TaXplIDE0IC9Sb290IDEgMCBSIC9JbmZvIDIgMCBSCi9JRCBbPDk5MDQ1MDYyQzNBODM2MkE2MDA4REE5NTdFM0JFOTFGPjw5OTA0NTA2MkMzQTgzNjJBNjAwOERBOTU3RTNCRTkxRj5dCj4+CnN0YXJ0eHJlZgo1MzUyCiUlRU9GCg==', //ty wstawiamy zakodowany w base64 obraz faktury
											'ImageFormat'	=>'PDF' //format faktury (np PDF)
											)
	);
	$data['LabelImageFormat']='PDF'; //format listu: PDF lub ZPL2
	$data['RequestArchiveDoc']='Y'; //generowanie listu: "WaybillDOC" - kopi listu przewozowego
	$data['Label']=array('LabelTemplate'=>'8X4_PDF'); //szablon listu - jest kilka szbalonów PDF i Thermal
	#usługi dodatkowe - jesli maja być
		$data['SpecialService'][]=array('SpecialServiceType'		=>'WY',); //PLT - TYlko jesli załaczamy elektroniczny obraz faktury (dla prodktów celny: P,Y,M,E,H)
		//$data['SpecialService'][]=array('SpecialServiceType'		=>'DD',); //DDP
		//$data['SpecialService'][]=array('SpecialServiceType'		=>'II',); //Ubezpieczenie
	$data['EProcShip']='N'; // Y -bez AWB,  N - z AWB . Y pozwala wysałać requesta do walidacji i nie otrzymamy do niego listu przewozowego

	//jesli celny wymagane, jesli nie celny można zignorować nie jest generowana ta sekcja w XML
	$data['Dutiable']=array(
							'DeclaredValue'		=>'22.56',	//zawsze w formacie xx.xx (zawsze czesci setne z rozdzielaczem w postaci kropki)
							'DeclaredCurrency'	=>'EUR',
							'TermsOfTrade'		=>'DAP'			//incoterms
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
<?php
class XMLPI{
   public $mode; // = (ENUM('test','live'))
   private $server;
   ################ Put below your dedicated credentials
   private $credentials = 
					array(
							'SiteID' => array(
											'test' => '***',		// test login
											'live'=>'***'		// live login
											),
							'Password' => array(
											'test' => '***',		// test password
											'live' => '***'		// live password
											)
						);
   private $xml_in;
   private $xml_out;
   private $xml_out_SimpleXML;
   private $xml_in_SimpleXML;
   private $xml_in_Array;
   private $xml_out_Array;

   public function XMLPI($mode){//
		$this->mode=$mode;
   }
	  public function __get($name){
          		return $this->$name;
	  }
	  
       function array_to_xml( $data, &$xml_data ) { //funkcja do zamiany tablicy w xmla
          foreach( $data as $key => $value ) {
                if( is_array($value) ) { //jesli jest to tablica nie znacznik
                          if ($value[0]) { //istnieje indeks liczbowy czyli będa pod tym samym kluczem głównym - np exportline item
								foreach($value as $key2=>$value2){
										$subnode = $xml_data->addChild($key);
										foreach($value2 as $key3=>$value3){
											if( is_array($value3) ) { //jesli jest tablicą{
												$subnode2 = $subnode->addChild($key3);
												foreach($value3 as $key4=>$value4){
													$subnode2->addChild($key4,htmlspecialchars("{$value4}"));
												}
											}else{
												$subnode->addChild($key3,htmlspecialchars("{$value3}"));
											}
										}
								}
                          }else{
							$subnode = $xml_data->addChild($key);
							foreach($value as $key2=>$value2){
										 if( is_array($value2) ) { //jesli jest tablicą
											  $subnode2 = $subnode->addChild($key2);
											  foreach($value2 as $key3=>$value3){
												$subnode2->addChild($key3,htmlspecialchars("{$value3}"));                                          
											  }
										 }else{
												$subnode->addChild($key2,htmlspecialchars("{$value2}"));                                      
										 }
							} 							   
                          }
                       
                } else { //nie jest tablicą - twórz dziecko z wartością
                     $xml_data->addChild("$key",htmlspecialchars("$value"));
                }
          }
     }
	  public function create_xml_out($data){
		  if ($this->mode=='test')
		  {
				// test server
			   $this->server='https://xmlpitest-ea.dhl.com/XMLShippingServlet';
		  }
		  elseif($this->mode=='live')
		  {
			// standard production server
			$this->server='https://xmlpi-ea.dhl.com/XMLShippingServlet';
		  }else{
			  die ('Nie określony tryb pracy test/live');
		  }
		$xml=new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?'.'><DHLXML/>');
			$Request=$xml->addChild('Request');
				$ServiceHeader = $Request->addChild('ServiceHeader');
				$ServiceHeader->addChild("MessageTime",date("c"));
				$ServiceHeader->addChild("MessageReference","XML Class DHL Express ESS PL");
				$ServiceHeader->addChild("SiteID",$this->credentials[SiteID][$this->mode]);
				$ServiceHeader->addChild("Password",$this->credentials[Password][$this->mode]);	
				$MetaData=$Request->addChild('MetaData');
					$MetaData->addChild('SoftwareName','XML Class DHLPL');
					$MetaData->addChild('SoftwareVersion','1.0');
			$xml->addChild('LanguageCode','en');
			$Billing=$xml->addChild('Billing');
				$Billing->addChild('ShipperAccountNumber',$data['ShipperAccountNumber']);
				$Billing->addChild('ShippingPaymentType','S');
			$Consignee=$xml->addChild('Consignee');
				$this->array_to_xml($data[Consignee],$Consignee);
				if (trim($xml->Consignee->AddressLine2)=='') unset($xml->Consignee->AddressLine2);
				if (trim($xml->Consignee->AddressLine3)=='') unset($xml->Consignee->AddressLine3);
			if ($data['IsDutiable']=='Y'){ //tylko jesli celna
				$Dutiable=$xml->addChild('Dutiable');
				$this->array_to_xml($data[Dutiable],$Dutiable);
				$xml->addChild('UseDHLInvoice','N'); 			//tworzenia faktury przez API  N/Y
				$xml->addChild('DHLInvoiceLanguageCode','en');	//jezyk faktury
				$xml->addChild('DHLInvoiceType','CMI');			//typ faktury CMI/PMI
				$ExportDeclaration=$xml->addChild('ExportDeclaration');
				$this->array_to_xml($data[ExportDeclaration],$ExportDeclaration);
			}
			$Reference=$xml->addChild('Reference');
				$this->array_to_xml($data[Reference],$Reference);
			$ShipmentDetails=$xml->addChild('ShipmentDetails');
				$this->array_to_xml($data[ShipmentDetails],$ShipmentDetails);
			$Shipper=$xml->addChild('Shipper');
				$this->array_to_xml($data[Shipper],$Shipper);				
				if (trim($xml->Shipper->AddressLine2)=='') unset($xml->Shipper->AddressLine2);
				if (trim($xml->Shipper->AddressLine3)=='') unset($xml->Shipper->AddressLine3);				
			if ($data[SpecialService]){ //SpecialServices przekazywane sa jako tablica indeksowana liczbowo
				foreach($data[SpecialService] as $value){
					if ($value['SpecialServiceType']=='WY'){
						if ($data['IsDutiable']=='Y'){ //dodaj WY tylko jesli jest celna
							$SpecialService=$xml->addChild('SpecialService');
							$this->array_to_xml($value,$SpecialService);							
						}
					}else{ //dodaj pozostałe usługi dodatkowe
						$SpecialService=$xml->addChild('SpecialService');
						$this->array_to_xml($value,$SpecialService);							
					}
				}
			}
			$xml->addChild('EProcShip',$data['EProcShip']);
			if ($data['IsDutiable']=='Y'){ //tylko jesli celna
				if ($data['DocImages']['DocImage']['Image']){ //tylko jesli jest kod base64 faktury
					$DocImages=$xml->addChild('DocImages');
						$this->array_to_xml($data[DocImages],$DocImages);
				}
			}
			$xml->addChild('LabelImageFormat',$data['LabelImageFormat']);
			$xml->addChild('RequestArchiveDoc',$data['RequestArchiveDoc']);
			$Label=$xml->addChild('Label');
				$this->array_to_xml($data[Label],$Label);

		$this->xml_out_SimpleXML=$xml;
		$this->xml_out=$xml->asXML();
		$this->xml_out=str_replace('<DHLXML>','<req:ShipmentRequest xmlns:req="http://www.dhl.com" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.dhl.com ship-val-global-req.xsd" schemaVersion="10.0">',$this->xml_out);
		$this->xml_out=str_replace('</DHLXML>','</req:ShipmentRequest>',$this->xml_out);
		//file_put_contents('xml_request.xml',$this->xml_out); //Zapisz request
	  }
	  
	  public function send_request_XML(){
		   $this->xml_in=utf8_encode($this->post_url ( $this->server, $this->xml_out)); //utf8_encode - funkcja kodująca w utf
		   if (strlen($this->xml_in)==0){
			   echo '</br>Brak odpowiedzi: '.$this->nazwa_pliku.' Kolejna próba nr 2</br>';
			   flush();
			   ob_flush();
			   sleep(2);
			   $this->xml_in=utf8_encode($this->post_url ( $this->server, $this->xml_out)); //utf8_encode - funkcja kodująca w utf
		   }
		   if (strlen($this->xml_in)==0){
			   echo '</br>Brak odpowiedzi: '.$this->nazwa_pliku.' Kolejna próba nr 3</br>';
			   flush();
			   ob_flush();
			   sleep(2);
			   $this->xml_in=utf8_encode($this->post_url ( $this->server, $this->xml_out)); //utf8_encode - funkcja kodująca w utf
		   }
		   $this->xml_in_SimpleXML  = simplexml_load_string($this->xml_in);
		   $this->xml_out_SimpleXML = simplexml_load_string($this->xml_out);
		   //file_put_contents('xml_response.xml'.$this->nazwa_pliku,$this->xml_in);
	  }
   public static function tab($tab,$tekst=''){
          if ($tekst){
             echo '<hr />';
             echo "<h3>{$tekst}</h3>";
          }
           echo '<pre>';
           print_r($tab);
           echo '</pre>';
          if ($tekst){echo '<hr />';}
      }
	  private function post_url($url, $data,$timeout=200) {
			$ch = curl_init (); //initiate the curl session
			curl_setopt ( $ch, CURLOPT_URL, $url ); //set to url to post to
			$headers = array(
						   "Content-Type: text/xml; charset=utf-8",
						   "SOAPAction: \"/soap/action/query\"",
						   "Content-length: " . strlen($data)
			);
			curl_setopt($ch, CURLOPT_PORT , 443);
			curl_setopt($ch, CURLOPT_VERBOSE, 0);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
			curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
			// ------------
			
			curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 ); // return data in a variable
			curl_setopt ( $ch, CURLOPT_POST, 1 );
			curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data ); // post the xml
			curl_setopt ( $ch, CURLOPT_TIMEOUT, ( int ) $timeout ); // set timeout in seconds
			curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
			$xmlResponse = curl_exec ( $ch );
			curl_close ( $ch );
			return $xmlResponse;
		}	
private function xml2array($url, $get_attributes = 1, $priority = 'tag',$local=false){
    $contents = "";
    if (!function_exists('xml_parser_create'))
    {
        return array ();
    }
    $parser = xml_parser_create('');
    if ($local){
         $contents=$url;
    }else{
         if (!($fp = @ fopen($url, 'rb')))
         {
             return array ();
         }
         while (!feof($fp))
         {
             $contents .= fread($fp, 8192);
         }
         fclose($fp);
    }
    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    xml_parse_into_struct($parser, trim($contents), $xml_values);
    xml_parser_free($parser);
    if (!$xml_values)
        return; //Hmm...
    $xml_array = array ();
    $parents = array ();
    $opened_tags = array ();
    $arr = array ();
    $current = & $xml_array;
    $repeated_tag_index = array ();
    foreach ($xml_values as $data)
    {
        unset ($attributes, $value);
        extract($data);
        $result = array ();
        $attributes_data = array ();
        if (isset ($value))
        {
            if ($priority == 'tag')
                $result = $value;
            else
                $result['value'] = $value;
        }
        if (isset ($attributes) and $get_attributes)
        {
            foreach ($attributes as $attr => $val)
            {
                if ($priority == 'tag')
                    $attributes_data[$attr] = $val;
                else
                    $result['attr'][$attr] = $val; //Set all the attributes in a array called 'attr'
            }
        }
        if ($type == "open")
        {
            $parent[$level -1] = & $current;
            if (!is_array($current) or (!in_array($tag, array_keys($current))))
            {
                $current[$tag] = $result;
                if ($attributes_data)
                    $current[$tag . '_attr'] = $attributes_data;
                $repeated_tag_index[$tag . '_' . $level] = 1;
                $current = & $current[$tag];
            }
            else
            {
                if (isset ($current[$tag][0]))
                {
                    $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                    $repeated_tag_index[$tag . '_' . $level]++;
                }
                else
                {
                    $current[$tag] = array (
                        $current[$tag],
                        $result
                    );
                    $repeated_tag_index[$tag . '_' . $level] = 2;
                    if (isset ($current[$tag . '_attr']))
                    {
                        $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                        unset ($current[$tag . '_attr']);
                    }
                }
                $last_item_index = $repeated_tag_index[$tag . '_' . $level] - 1;
                $current = & $current[$tag][$last_item_index];
            }
        }
        elseif ($type == "complete")
        {
            if (!isset ($current[$tag]))
            {
                $current[$tag] = $result;
                $repeated_tag_index[$tag . '_' . $level] = 1;
                if ($priority == 'tag' and $attributes_data)
                    $current[$tag . '_attr'] = $attributes_data;
            }
            else
            {
                if (isset ($current[$tag][0]) and is_array($current[$tag]))
                {
                    $current[$tag][$repeated_tag_index[$tag . '_' . $level]] = $result;
                    if ($priority == 'tag' and $get_attributes and $attributes_data)
                    {
                        $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                    }
                    $repeated_tag_index[$tag . '_' . $level]++;
                }
                else
                {
                    $current[$tag] = array (
                        $current[$tag],
                        $result
                    );
                    $repeated_tag_index[$tag . '_' . $level] = 1;
                    if ($priority == 'tag' and $get_attributes)
                    {
                        if (isset ($current[$tag . '_attr']))
                        {
                            $current[$tag]['0_attr'] = $current[$tag . '_attr'];
                            unset ($current[$tag . '_attr']);
                        }
                        if ($attributes_data)
                        {
                            $current[$tag][$repeated_tag_index[$tag . '_' . $level] . '_attr'] = $attributes_data;
                        }
                    }
                    $repeated_tag_index[$tag . '_' . $level]++; //0 and 1 index is already taken
                }
            }
        }
        elseif ($type == 'close')
        {
            $current = & $parent[$level -1];
        }
    }
    return ($xml_array);
}	
}
?>

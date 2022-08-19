<?php
namespace DHL;
use DHL\Entity\GB\ShipmentRequest;
use DHL\Entity\GB\ShipmentResponse;
use DHL\Client\Web as WebserviceClient;

/**
 * A service class that wraps the main calls that can be done to DHL 
 */
class Service
{
    /**
     * Error message on last call
     * @var string
     */
    public $errorMessage = null;

    /**
     * Client to DHL webservice
     * @var WebserviceClient
     */
    protected $_client = null;

    /**
     * Class constructor
     */ 
    public function __construct(WebserviceClient $client)
    {
        $this->_client = $client;
    }

    /**
     * Send a shipment request to DHL
     *
     * @param ShipmentRequest Request to send
     *   
     * @return ShipmentResponse The Shipment response object upon success, false otherwise
     */
    public function sendShipmentRequest(ShipmentRequest $request)
    {
        // Call DHL XML API
        try 
        {
            $xml = $this->_client->call($request);
            $response = new ShipmentResponse();
            $response->initFromXML($xml);
        }
        catch (\Exception $e) 
        {
            $this->errorMessage = $e->getMessage();
            return false;
        }
    
        return $response;
    }
}

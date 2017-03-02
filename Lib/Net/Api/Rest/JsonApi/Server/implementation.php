<?php
/**
  * PHPGoodies:Lib_Api_Rest_JsonApi_Server - Provides a JSON:API compliant HTTP REST Server
  *
  * @uses Oop_Type
  * @uses Oop_Exception_TypeMismatch
  * @uses Lib_Data_Hash
  * @uses Lib_Net_Http_Request
  *
  * @author Sean M. Kelly <smk@smkelly.com>
  */

namespace PHPGoodies;

PHPGoodies::import('Oop.Type');
PHPGoodies::import('Oop.Exception.TypeMismatch');
PHPGoodies::import('Lib.Net.Http.Request');

/**
 * JSON:API Server
 */
class Lib_Net_Api_Rest_JsonApi_Server {

	/**
	 * URL which forms the server base to which all URIs are relative
	 */
	protected $baseUrl;

	/**
	 * Cached copy of the endpoint map
	 */
	protected $resourceMap;

	/**
	 * Constructor
	 *
	 * @param String $baseUrl URL which forms the server base to which all URIs are relative
	 */
	public function __construct($baseUrl) {
		$this->baseUrl = Oop_Type::requireType('string', $baseUrl);
		$this->resourceMap = PHPGoodies::instantiate('Lib.Data.Hash');
	}

	/**
	 * Add an endpoint class to handle requests
	 */
	public function addEndpoint($endpointClassName) {
		Oop_Type::requireType('string', $endpointClassName);
		if (! PHPGoodies::classDefined($resourceClassName)) {
			throw new \Exception("Tried to add endpoint with undefined class name: '{$endpointClassName}'");
		}

		return $this;
	}

	/**
	 * HTTP Request Processor
	 *
	 * @param $httpRequest HttpRequest object instance reference to request to be processed
	 *
	 * @return HttpResponse object instance with the result of the processed request
	 */
	public function processRequest(&$httpRequest) {

		// Check our parameters
		try {
			Oop_Type::requireType($httpRequest, 'class:Lib_Net_Http_Request');
		}
		catch (Oop_Exception_TypeMismatch $e) {
			return $this->responseError(
				Lib_Net_Http_Response::HTTP_INTERNAL_SERVER_ERROR,
				'Invalid Arguments to Process Request'
			);
		}

		try {
			$requestInfo = $httpRequest->getInfo();
			$controller = $this->getControllerForUri($requestInfo->uri);
			if (is_null($controller) || (! $controller instanceof Lib_Net_Api_Rest_JsonApi_Server_Controller)) {
				return $this->responseError(
					Lib_Net_Http_Response::HTTP_NOT_FOUND,
					'No mapped controller for request URI'
				);
			}

			// Translate from HTTP request method to Resource operation
			switch ($requestInfo->method) {
				case Lib_Net_Http_Request::HTTP_GET: return $this->processRequestRetrieve($requestInfo, $resource);
				case Lib_Net_Http_Request::HTTP_POST: return $this->processRequestCreate($requestInfo, $resource);
			}

			// TODO: Execute the appropriate handler method for the given request method against the resource that we received
			// How shall we go about this?
			//  * Switch on request method and call an abstract resource method?
			//  * Call a resource method named after the request method (no switch case) ?
			//  * Use some sort of decorator pattern solution to get a variant of the resource class with just a call method linked to the correct operations based on the request method?
			//  * Call some resource call() method, handing it the request method?
			//  * Call some resource process() method, handing it the entire httpRequest?
		}
		catch (\Exception $e) {
			return $this->responseError(
				Lib_Net_Http_Response::HTTP_INTERNAL_SERVER_ERROR,
				'Unexpected internal error: ' . $e->getMessage()
			);
		}
	}

	/**
	 * Process the request as a resource creation
	 *
	 * @param $requestInfo Object with properties describing the request being made
	 * @param $resource Object class instance implementing Resource interface
	 */
	private function processRequestCreate($requestInfo, $resource) {
		try {
			// Get the request POST data into the resource
			$jsonApiDocument = json_decode($requestInfo->data);
			// TODO: Validate the jsonApiDocument - we can't accept just any old thing...
			$resource->fromJson(
			);
			$resource->create();
			// Form a successful response
			$httpResponse = $this->createHttpResponse(
				Lib_Net_Http_Response::HTTP_CREATED
			);
			// Formulate a location redirect header to the created resource
			$httpResponse->headers->set('Location', "{$this->baseUrl}{$resource->getUri()}");
			return $httpResponse;
		}
		catch (\Exception $e) {
			// TODO: catch different exceptions depending on what went wrong!
			return $this->responseError(
				Lib_Net_Http_Response::HTTP_BAD_REQUEST,
				'Failed creation: ' . $e->getMessage()
			);
		}
	}

	/**
	 * Process the request as a resource retrieval
	 *
	 * @param $requestInfo Object with properties describing the request being made
	 * @param $resource Object class instance implementing Resource interface
	 */
	private function processRequestRetrieve($requestInfo, $resource) {
		try {
			$resource->retrieve();
			// Form a successful response
			return $this->createHttpResponse(
				Lib_Net_Http_Response::HTTP_OK,
				$resource->toJson()
			);
		}
		catch (\Exception $e) {
			// TODO: catch different exceptions depending on what went wrong!
			return $this->responseError(
				Lib_Net_Http_Response::HTTP_BAD_REQUEST,
				'Failed retrieval: ' . $e->getMessage()
			);
		}
	}

	/**
	 * Get the matching resource from the resource map for the given URI
	 *
	 * @param $uri string URI for the request being processed
	 */
	private function getResourceForUri($uri) {
		// TODO: Find and return an instance of the first resource that matches this uri in the resource map (they probably all need to be regular expressions...)
	}

	/**
	 * Creates an HttpResponse
	 */
	private function createHttpResponse($code, $body = null) {
		// Form a full-blown HttpResponse
		$httpResponse = PHPGoodies::instantiate('Lib.Net.Http.Response');
		$httpResponse->headers->set('Content-type', 'application/vnd.api+json');
		$httpResponse->setCode($code);
		if (! is_null($body)) $httpResponse->setBody(json_encode($body));
		return $httpResponse;
	}

	/**
	 * Produce an error response
	 *
	 * @param $code Integer HTTP status code
	 * @param $message String readable message text explaining the error
	 */
	private function responseError($code, $message) {

		// Form a simple JSON:API Response Document
		$body = new \StdClass();

		// Form a simple JSON:API ErrorObject
		$errorObject = new \StdClass();
		$errorObject->detail = $message;
		$body->errors = array($message);

		return $this->createHttpResponse($code, $body);
	}
}


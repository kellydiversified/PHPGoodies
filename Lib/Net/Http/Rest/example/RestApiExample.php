<?php
/**
 * PHPGoodies RequestInfoExample.php
 *
 * @author Sean M. Kelly <smk@smkelly.com>
 */

// 1) Adapt the name-spaced goodies to the global namespace
use PHPGoodies\PHPGoodies as PHPGoodies;
use PHPGoodies\HttpResponse as HttpResponse;
use PHPGoodies\RestEndpoint as RestEndpoint;

// 2) Load up our goodies
require(realpath(dirname(__FILE__) . '/../../../../../PHPGoodies.php'));
PHPGoodies::import('Lib.Net.Http.HttpResponse');
PHPGoodies::import('Lib.Net.Http.Rest.RestEndpoint');
$api = PHPGoodies::instantiate('Lib.Net.Http.Rest.RestApi', '/api/2', 'General Api', 2);

// 3) Make a custom API endpoint
class ChronometricsEndpoint extends RestEndpoint {
	public function get($requestInfo) {
		$response = PHPGoodies::instantiate('Lib.Net.Http.Rest.JsonResponse');
		$response->dto->setProperties(array(
			'currentTime' => date('Y-m-d h:m:s')
		));
		$response->code = HttpResponse::HTTP_OK;
		return $response;
	}
}
$api->addEndpoint('/api/2/chronometrics', new ChronometricsEndpoint());

// 4) Now mock up an HTTP request to see how the endpoint responds
$_SERVER['HTTP_HOST'] = 'localhost';
$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REQUEST_SCHEME'] = 'http';
$_SERVER['REQUEST_URI'] = '/api/2/chronometrics';

$httpResponse = $api->getResponse();
$api->respond($httpResponse);


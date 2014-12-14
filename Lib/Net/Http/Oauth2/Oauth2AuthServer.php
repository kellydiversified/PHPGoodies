<?php
/**
 * PHPGoodies:Oauth2AuthServer - Oauth2 Authorization Server
 *
 * ref: http://tools.ietf.org/html/rfc6749
 *
 * @uses Hash
 * @uses HttpRequest
 * @uses Oauth2AuthDbIfc
 * @uses Oauth2AccessTokenIfc
 *
 * @author Sean M. Kelly <smk@smkelly.com>
 */

namespace PHPGoodies;

PHPGoodies::import('Lib.Net.Http.Oauth2.Oauth2AuthDbIfc');
PHPGoodies::import('Lib.Net.Http.Oauth2.Oauth2AccessTokenIfc');

/**
* Oauth2 Authorization Server
*/
class Oauth2AuthServer {

	/**
	 * Enforce requests be made over TLS (HTTPS)
	 */
	protected $requireTls = true;

	/**
	 * The registered clients that we will recognize
	 */
	protected $registeredClients;

	/**
	 * The Oauth2AuthDbIfc instance we'll use to check user credentials
	 */
	protected $authdb;

	/**
	 * The Oauth2AccessToken instance we'll use to encode and decode tokens
	 */
	protected $accessToken;

	/**
	 * Constructor; dependency injection
	 *
	 * @param object $authDb A class instance implementing Oauth2AuthDbIfc interface
	 * @param object $accessToken A class instance of Oauth2AccessTokenIfc
	 */
	public function __construct(&$authDb, &$accessToken) {

		// Capture reference to the authDb; it is read-only for us so we don't need a copy
		if (! $authDb instanceof Oauth2AuthDbIfc) {
			throw new \Exception('Something other than an Oauth2AuthDbIfc supplied for the AuthDb');
		}
		$this->authDb =& $authDb;

		if (! $accessToken instanceof Oauth2AccessTokenIfc) {
			throw new \Exception('Something other than an Oauth2AccessTokenIfc supplied for the AccessToken');
		}
		$this->accessToken =& $accessToken;

		$this->registeredClients = PHPGoodies::instantiate('Lib.Data.Hash');
	}

	/**
	 * Setter for require TLS state
	 *
	 * @param boolean $state True to require TLS for all requests, false to make TLS optional
	 *
	 * @return object $this for chaining support...
	 */
	public function setRequireTls($state) {
		$this->requreTls = $state ? true : false;
	}

	/**
	 * Add a registered client to the list
	 *
	 * @param string $clientId The oauth2 client_id
	 * @param string $clientSecret The oauth2 client_secret
	 *
	 * @return object $this for chaining support...
	 */
	public function addRegisteredClient($clientId, $clientSecret) {
		$this->registeredClients->add($clientId, $clientSecret);
		return $this;
	}

	/**
	 * Check whether we have a registered client with the specified clientId
	 *
	 * @param string $clientId The oauth2 client_id
	 *
	 * @return boolean true if we have the clientId registered, else false
	 */
	public function hasRegisteredClient($clientId) {
		return $this->registeredClients->has($clientId);
	}

	/**
	 * Check whether the supplied secret matches that of the specified client
	 *
	 * @param string $clientId The oauth2 client_id
	 * @param string $clientSecret The oauth2 client_secret
	 *
	 * @return boolean true if the secrets match, else false
	 */
	protected function doesSecretMatchRegisteredClient($clientId, $secret) {
		if (! $this->hasRegisteredClient) return false;
		$clientSecret = $this->registeredClients->get($clientId);
		return ($secret === $clientSecret);
	}

	/**
	 * Check whether the HttpRequest's credentials are for an authorized client
	 *
	 * From RFC:
	 * The authorization server MUST support the HTTP Basic authentication scheme for
	 * authenticating clients that were issued a client password.
	 *
	 * @param object $httpRequest An HttpRequest instance
	 *
	 * @return boolean true if request has a registered client with password match, else false
	 */
	protected function isAuthorizedClient(&$httpRequest) {

		// Make sure we got an HttpRequest object
		if (! $httpRequest instanceof HttpRequest) {
			throw new \Exception('Something other than an HttpRequest supplied');
		}

		// Get the request data and ensure that it has an Authorization header
		$request = $httpRequest->getInfo();
		if (! $request->headers->has('Authorization')) return false;

		// Extract clientId and clientSecret from the Authorization header
		$auth = base64_decode($request->headers->get('Authorization'));
		list ($clientId, $secret) = explode(':', $auth);

		// The request is an authorized client if there's a registered match
		return $this->doesSecretMatchRegisteredClient($clientId, $secret);
	}

	/**
	 * Handle requests for the token endpoint
	 *
	 * @param object $httpRequest An HttpRequest instance
	 *
	 */
	public function getAccessToken($httpRequest) {

		// Request must be accompanied by an authorized client
		if (! $this->isAuthorizedClient($httpRequest)) return null;

		// Get the request data and ensure that it meets our requirements
		$request = $httpRequest->getInfo();
		if ($this->requireTls && ($request->protocol != 'HTTPS')) return null;
		if ($request->method != 'POST') return null;

		// Check the supplied user credentials
		$username = $request->data->get('username');
		$password = $request->data->get('password');
		$userId = $this->authDb->checkCredentials($username, $password);
		if (null == $userId) return false;

		// Generate a token string
		$tokenData = array(
			'tokenType' => 'bearer',
			'expires' => 0, // TODO set this to some future timestamp after which the token will no longer be accepted
			'userId' => $userId,
			'scope' => '' // TODO what scopes does this userId have access to with this token?
		);
		$tokenString = $this->accessToken->toString($tokenData);

		return $tokenString;
	}
}


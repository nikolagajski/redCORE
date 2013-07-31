<?php
/**
 * @package     RedRad
 * @subpackage  OAuth2
 *
 * This work is based on a Louis Landry work about oauth1 server suport for Joomla! Platform.
 * URL: https://github.com/LouisLandry/joomla-platform/tree/9bc988185ccc3e1c437256cc2c927e49312b3d00/libraries/joomla/oauth1
 *
 * @copyright   Copyright (C) 2012 - 2013 redCOMPONENT.com. All rights reserved.
 * @license     GNU General Public License version 2 or later, see LICENSE.
 */

defined('JPATH_PLATFORM') or die;

/**
 * OAuth Controller class for initiating temporary credentials for the RedRAD.
 *
 * @package     RedRAD
 * @subpackage  OAuth2
 * @since       1.0
 */
class ROAuth2ControllerResource extends ROAuth2ControllerBase
{
	/**
	 * Constructor.
	 *
	 * @param   JRegistry        $options      ROAuth2User options object
	 * @param   JHttp            $http         The HTTP client object
	 * @param   JInput           $input        The input object
	 * @param   JApplicationWeb  $application  The application object
	 *
	 * @since   1.0
	 */
	public function __construct(ROAuth2Request $request = null, ROAuth2Response $response = null)
	{
		// Call parent first
		parent::__construct();

		// Setup the autoloader for the application classes.
		JLoader::register('ROAuth2Request', JPATH_REDRAD.'/oauth2/protocol/request.php');
		JLoader::register('ROAuth2Response', JPATH_REDRAD.'/oauth2/protocol/response.php');

		$this->request = isset($request) ? $request : new ROAuth2Request;
		$this->response = isset($response) ? $response : new ROAuth2Response;
	}

	/**
	 * Handle the request.
	 *
	 * @return  void
	 *
	 * @since   1.0
	 */
	public function execute()
	{
		// Verify that we have an OAuth 2.0 application.
		if ((!$this->app instanceof ApiApplicationWeb))
		{
			throw new LogicException('Cannot perform OAuth 2.0 authorisation without an OAuth 2.0 application.');
		}

		// We need a valid signature to do initialisation.
		if (!$this->request->client_id || !$this->request->client_secret || !$this->request->signature_method )
		{
			$this->app->sendInvalidAuthMessage('Invalid OAuth request signature.');

			return 0;
		}

		// Generate temporary credentials for the client.
		$credentials = $this->createCredentials();

		// Getting the client object
		$client = $this->fetchClient($this->request->client_id);

		// Doing authentication using Joomla! users
		$this->request->doOAuthAuthentication($client->_identity->password);

		// Load the JUser class on application for this client
		$this->app->loadIdentity($client->_identity);

/*
		// Build the response for the client.
		$response = array(
			'oauth_code' => $credentials->getTemporaryToken(),
			'oauth_state' => true
		);

		// Set the response code and body.
		$this->response->setHeader('status', '200')
			->setBody(json_encode($response))
			->respond();
*/
	}

} // end class

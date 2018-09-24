<?php

namespace Elastest\Controllers;

use \Elastest\Http\HttpRequestInterface;
use \Elastest\Http\HttpResponseInterface;

/**
 *  This controller is called when a user should be authorized
 *  by an authorization server.  As OAuth2 does not handle
 *  authorization directly, this controller ensures the request is valid, but
 *  requires the application to determine the value of $is_authorized
 *
 *  @code
 *      $user_id = $this->somehowDetermineUserId();
 *      $is_authorized = $this->somehowDetermineUserAuthorization();
 *      $response = new OAuth2\Response();
 *      $authorizeController->handleAuthorizeRequest(
 *          OAuth2\Request::createFromGlobals(),
 *          $response,
 *          $is_authorized,
 *          $user_id
 *      );
 *      $response->send();
 * @endcode
 */
interface BooksControllerInterface {
    /**
     * List of possible authentication response types.
     * The "authorization_code" mechanism exclusively supports 'code'
     * and the "implicit" mechanism exclusively supports 'token'.
     *
     * @var string
     * @see http://tools.ietf.org/html/rfc6749#section-4.1.1
     * @see http://tools.ietf.org/html/rfc6749#section-4.2.1
     */
    const RESPONSE_TYPE_AUTHORIZATION_CODE = 'code';
    const RESPONSE_TYPE_ACCESS_TOKEN = 'token';

    /**
     * Handle the Books request
     *
     * @param HttpRequestInterface $request
     * @param HttpResponseInterface $response
     * @param $is_authorized
     * @param null $user_id
     * @return mixed
     */
    public function handleAuthorizeRequest(HttpRequestInterface $request, HttpResponseInterface $response, $is_authorized, $user_id = null);

    /**
     * @param HttpRequestInterface $request
     * @param HttpResponseInterface $response
     * @return bool
     */
    public function validateAuthorizeRequest(HttpRequestInterface $request, HttpResponseInterface $response);
}

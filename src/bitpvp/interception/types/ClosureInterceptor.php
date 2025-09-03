<?php

namespace bitpvp\interceptor\types;

use bitpvp\interceptor\Interceptor;
use bitpvp\interceptor\InterceptorResult;
use pocketmine\network\mcpe\NetworkSession;

final class ClosureInterceptor implements Interceptor {

	/**
	 * @param \Closure(NetworkSession, string &$message, bool): InterceptorResult $interceptor
	 */
	public function __construct(
		protected \Closure $interceptor
	){

	}

	/***
	 * @param NetworkSession $session
	 * @param string         $message
	 * @param bool           $fromClient
	 *
	 * @return InterceptorResult
	 */
	public function intercept(NetworkSession $session, string &$message, bool $fromClient) : InterceptorResult {
		return ($this->interceptor)($session, $message, $fromClient);
	}
}
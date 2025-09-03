<?php

namespace bitpvp\interceptor\types;

use bitpvp\interceptor\Interceptor;
use bitpvp\interceptor\InterceptorResult;
use pocketmine\network\mcpe\NetworkSession;

class StandardClientInterceptor implements Interceptor{

	/**
	 * @param string $search
	 * @param string $replace
	 */
	public function __construct(
		protected string $search,
		protected string $replace
	) {

	}

	/**
	 * @param NetworkSession $session
	 * @param string         $message
	 * @param bool           $fromClient
	 *
	 * @return InterceptorResult
	 */
	public function intercept(NetworkSession $session, string &$message, bool $fromClient) : InterceptorResult{
		if($fromClient){
			$message = str_replace($this->search, $this->replace, $message);
		}
		return InterceptorResult::INTERCEPTION_CONTINUE;
	}
}
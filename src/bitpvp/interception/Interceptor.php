<?php

namespace bitpvp\interceptor;

use pocketmine\network\mcpe\NetworkSession;

interface Interceptor {

	/**
	 * @param NetworkSession $session
	 * @param string         $message
	 * @param bool           $fromClient
	 *
	 * Interception, if ABORT returned, the callback will be ignored
	 * @return InterceptorResult
	 */
	public function intercept(NetworkSession $session, string &$message, bool $fromClient): InterceptorResult;
}
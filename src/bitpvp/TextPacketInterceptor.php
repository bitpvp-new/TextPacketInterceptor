<?php

namespace bitpvp;

use bitpvp\handling\InterceptorHandler;
use muqsit\simplepackethandler\SimplePacketHandler;
use pocketmine\event\EventPriority;
use pocketmine\plugin\Plugin;

final class TextPacketInterceptor{

	/**
	 * @param Plugin   $plugin the registrant
	 * @param \Closure $callback(TextPacket, NetworkSession)
	 *
	 * @return InterceptorHandler
	 */
	static public function create(Plugin $plugin, \Closure $callback): InterceptorHandler {
		return new InterceptorHandler(SimplePacketHandler::createInterceptor($plugin, EventPriority::HIGHEST), $callback);
	}
}
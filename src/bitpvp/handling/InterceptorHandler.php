<?php

namespace bitpvp\handling;

use bitpvp\interceptor\Interceptor;
use bitpvp\interceptor\InterceptorResult;
use Closure;
use muqsit\simplepackethandler\interceptor\IPacketInterceptor;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\TextPacket;

final class InterceptorHandler {

	/** @var array<int, Interceptor> */
	protected array $interceptors = [];

	/**
	 * @param IPacketInterceptor $handler
	 * @param Closure            $callback
	 */
	public function __construct(
		protected IPacketInterceptor $handler,
		protected Closure $callback
	){

	}

	/**
	 * @return Closure
	 */
	public function getCallback() : Closure{
		return $this->callback;
	}

	/**
	 * @return IPacketInterceptor
	 */
	public function getHandler() : IPacketInterceptor{
		return $this->handler;
	}

	/**
	 * @return array
	 */
	public function getInterceptors() : array{
		return $this->interceptors;
	}

	/**
	 * @param TextPacket     $packet
	 * @param NetworkSession $session
	 * @param bool           $packetFromClient
	 *
	 * @return void
	 */
	protected function onIntercept(TextPacket $packet, NetworkSession $session, bool $packetFromClient): void {
		$originalMessage = $packet->message;
		$message = $packet->message;

		foreach($this->interceptors as $interceptor){
			$result = $interceptor->intercept($session, $message, $packetFromClient);

			if($result === InterceptorResult::INTERCEPTION_ABORT){
				$packet->message = $originalMessage;
				return;
			}
		}

		$packet->message = $message;
		($this->callback)($packet, $session);
	}

	/**
	 * @param Interceptor ...$interceptors
	 *
	 * @return void
	 */
	public function registerInterceptors(Interceptor ...$interceptors) : void{
		foreach($interceptors as $interceptor){
			$this->interceptors[spl_object_id($interceptor)] = $interceptor;
		}
	}

	/**
	 * @param Interceptor $interceptor
	 *
	 * @return void
	 */
	public function registerInterceptor(Interceptor $interceptor) : void{
		$this->interceptors[spl_object_id($interceptor)] = $interceptor;
	}

	/**
	 * @param Interceptor $interceptor
	 *
	 * @return void
	 */
	public function unregisterInterceptor(Interceptor $interceptor): void {
		$id = spl_object_id($interceptor);
		if(isset($this->interceptors[$id])){
			unset($this->interceptors[$id]);
		}
	}

	/**
	 * @return void
	 */
	public function clearInterceptors() : void{
		$this->interceptors = [];
	}

	/**
	 * @return void
	 */
	public function startIntercepting(): void {
		$this->handler
			->interceptIncoming(function(TextPacket $packet, NetworkSession $session): bool {
				$this->onIntercept($packet, $session, true);
				return true;
			})
			->interceptOutgoing(function(TextPacket $packet, NetworkSession $session): bool {
				$this->onIntercept($packet, $session, false);
				return true;
			});
	}
}
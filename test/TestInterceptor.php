<?php

use bitpvp\interceptor\InterceptorResult;
use pocketmine\network\mcpe\NetworkSession;
use pocketmine\network\mcpe\protocol\TextPacket;

class TestInterceptor extends \pocketmine\plugin\PluginBase{

	protected function onEnable() : void{
		$interceptor = \bitpvp\TextPacketInterceptor::create($this, function(TextPacket $packet, NetworkSession $session): void {
			// rn, I don't want to do smth else
		});

		$interceptor->registerInterceptors(
			new \bitpvp\interceptor\types\StandardClientInterceptor("&", ""), // e.g., if in chat uses TextFormat::colorize()
			new \bitpvp\interceptor\types\StandardClientInterceptor(\pocketmine\utils\TextFormat::ESCAPE, ""), // otherwise
			new \bitpvp\interceptor\types\ClosureInterceptor(function (NetworkSession $session, string &$message, bool $fromClient): InterceptorResult {
				if($fromClient){
					//serverbound, in our hcf we cancel the PlayerChatEvent and send a broadcast
					return InterceptorResult::INTERCEPTION_CONTINUE;
				}

				/**
				 * @var \pocketmine\player\Player|bitpvp\hcf\player\Player|null $player
				 */
				$player = $session->getPlayer();

				if(!$player instanceof bitpvp\hcf\player\Player){
					return InterceptorResult::INTERCEPTION_CONTINUE; //no player found but not needed to abort
				}

				$faction = $player->getFaction();

				if($faction !== null){
					$factionName = preg_quote($faction->getName(), '/');

					$letters = str_split($factionName);
					$pattern = '/(?:&[0-9a-fk-or])*' . implode('(?:&[0-9a-fk-or])*', $letters) . '(?:&[0-9a-fk-or])*/i';

					$message = preg_replace_callback($pattern, function ($matches) use ($faction) {
						return TextFormat::GREEN . $faction->getName();
					}, $message);
				}

				return InterceptorResult::INTERCEPTION_CONTINUE;
			})
		);

		$interceptor->startIntercepting();
	}
}
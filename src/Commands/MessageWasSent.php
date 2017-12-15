<?php

namespace Tpojka\Confer\Commands;

use App\Commands\Command;
use Illuminate\Contracts\Bus\SelfHandling;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldBeQueued;
use Tpojka\Confer\Message;
use Tpojka\Confer\Confer;
use Push;

class MessageWasSent extends Command implements SelfHandling, ShouldBeQueued {
	
	use InteractsWithQueue;

	protected $message;
	protected $confer;

	public function __construct(Message $message)
	{
		$this->message = $message;
		$this->confer = new Confer();
	}

	/**
	 * Handle the command.
	 */
	public function handle()
	{
		$conversation = $this->message->conversation;
		$conversation->touch();
		if ($conversation->isGlobal())
		{
			Push::trigger($this->confer->global, 'NewMessageWasPosted', $this->message->getEventData('global'));
		} else {
			Push::trigger($this->message->conversation->getChannel(), 'NewMessageWasPosted', $this->message->getEventData());
			Push::trigger($this->message->conversation->getChannel(), 'UserStoppedTyping', ['user' => $this->message->sender->id]);
		}
	}

}
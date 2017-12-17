<?php

namespace Tpojka\Confer\Commands;

use Tpojka\Confer\Confer;
use Tpojka\Confer\Message;
use Tpojka\Confer\Facades\Push;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class MessageWasSent implements ShouldQueue {
	
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

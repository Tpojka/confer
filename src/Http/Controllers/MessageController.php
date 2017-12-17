<?php

namespace Tpojka\Confer\Http\Controllers;

use Auth;
use App\User;
use Tpojka\Confer\Confer;
use Tpojka\Confer\Message;
use Illuminate\Http\Request;
use Tpojka\Confer\Conversation;
use App\Http\Controllers\Controller;
use Tpojka\Confer\Commands\MessageWasSent;

class MessageController extends Controller {
	
	protected $user;
	protected $confer;

	public function __construct(Confer $confer)
	{
		$this->middleware('auth');
                $this->middleware(function ($request, $next) use($confer) {

                    $this->user = Auth::user();
                    $this->confer = $confer;

                    return $next($request);
                });
	}

	/**
	 * Store a new instance of a message in the conversation
	 * 
	 * @param  Conversation $conversation
	 * @param  Request      $request
	 * @return Response
	 */
	public function store(Conversation $conversation, Request $request)
	{
		$message = Message::create([
			'body' => config('confer.enable_emoji') ? confer_convert_emoji_to_shortcodes(strip_tags($request->input('body'))) : strip_tags($request->input('body')),
			'conversation_id' => $conversation->id,
			'sender_id' => $this->user->id,
			'type' => 'user_message'
		]);
		$this->dispatch(new MessageWasSent($message));
		return $message->load('sender');
	}

}

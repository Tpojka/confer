<?php

namespace Tpojka\Confer\Http\Controllers;

use Push;
use Auth;
use App\User;
use Tpojka\Confer\Confer;
use Illuminate\Http\Request;
use Tpojka\Confer\Conversation;
use App\Http\Controllers\Controller;
use Tpojka\Confer\Commands\ParticipantLeft;
use Tpojka\Confer\Commands\ParticipantsWereAdded;
use Tpojka\Confer\Commands\ConversationWasRequested;
use Tpojka\Confer\Http\Requests\InviteParticipantsRequest;

class ConversationController extends Controller {
	
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

	public function test()
	{
		dd($this->confer->getUsersState());
		return view('confer::test');
		dd($this->user->getPresenceData());
	}

	public function settings()
	{
		return view('confer::settings');
	}

	/**
	 * Authenticate the user with Pusher
	 * 
	 * @param  Request $request
	 * @return Response
	 */
	public function authenticate(Request $request)
	{
		//if (! Auth::check()) abort(404);
		if ($this->isPresenceChannel($request->input('channel_name')))
		{
			$presenceData = $this->user->getPresenceData();

			return Push::presence_auth($request->channel_name, $request->socket_id, $this->user->id, $presenceData);
		}
		return Push::socket_auth($request->input('channel_name'), $request->socket_id);
	}

	/**
	 * Identify if the channel name indicates it is a presence channel
	 * 
	 * @param  String  $channelName
	 * @return boolean
	 */
	private function isPresenceChannel($channelName)
	{
		return strpos($channelName, 'presence') !== false;
	}

	public function index()
	{
		$conversations = $this->user->conversations()->has('messages')->with('messages.sender')->orderBy('updated_at', 'DESC')->get();
		return view('confer::conversationlist', compact('conversations'));
	}

	public function barIndex()
	{
		$conversations = $this->user->getBarConversations();
		return view('confer::barconversationlist', compact('conversations'));
	}

	public function info(Conversation $conversation)
	{
		return ['conversation' => $conversation];
	}

	public function requested(Conversation $conversation)
	{
		$this->dispatch(new ConversationWasRequested($conversation, $this->user));
	}

	/**
	 * Get a status list of the users in the confer system
	 * 
	 * @return Response
	 */
	public function listUsers()
	{
		$users = $this->confer->getUsersState();
		return view('confer::userlist', compact('users'));
	}

	public function getUserInfo(User $user)
	{
		return $user;
	}

	public function getUserAndConversationInfo(User $user, Conversation $conversation)
	{
		return ['user' => $user, 'conversation' => $conversation];
	}

	public function create(Request $request)
	{

	}

	/**
	 * Find a conversation between the user and another, or create it
	 * 
	 * @param  User   $user
	 * @return Response
	 */
	public function find(User $user)
	{
		$conversation = Conversation::findOrCreateBetween($this->user, $user);
		$this->dispatch(new ConversationWasRequested($conversation, $this->user));
		return $this->show($conversation);
	}

	/**
	 * Show a conversation
	 * 
	 * @param  Conversation $conversation
	 * @return Response
	 */
	public function show(Conversation $conversation)
	{
		$conversation->load('participants');
		$messages = $conversation->messages()->with('sender')->latest()->take(5)->get()->sortBy('created_at');
		return view('confer::conversation', compact('conversation', 'messages'));
	}

	public function showMoreMessages(Conversation $conversation, Request $request)
	{
		$current_message = $request->input('from_message');
		$messages = $conversation->messages()->with('sender')->where('id', '<', $current_message)->latest()->take(5)->get()->sortBy('created_at');
		return $messages;
	}

	public function showInvite(Conversation $conversation)
	{
		$potential_invitees = $conversation->getPotentialInvitees();
		return view('confer::invite', compact('conversation', 'potential_invitees'));
	}

	public function update(Conversation $conversation, InviteParticipantsRequest $request)
	{
		if ($request->has('conversation_name'))
		{
			$conversation = $conversation->createNewWithAdditionalParticipants($request->input('invited_users'), $request->input('conversation_name'));
		} else {
			$conversation->addAdditionalParticipants($request->input('invited_users'));
		}
		$this->dispatch(new ParticipantsWereAdded($conversation, $request->input('invited_users'), $this->user, $request->has('conversation_name')));
		return $conversation;
	}

	public function leave(Conversation $conversation)
	{
		$this->user->leaveConversation($conversation);
		$this->dispatch(new ParticipantLeft($conversation, $this->user));
		return ['success' => true];
	}

}

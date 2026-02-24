<?php

use App\User;
use Tpojka\Confer\Conversation;
use Illuminate\Support\Facades\Route;
use Tpojka\Confer\Http\Controllers\MessageController;
use Tpojka\Confer\Http\Controllers\SessionController;
use Tpojka\Confer\Http\Controllers\ConversationController;

Route::model('conferconversation', Conversation::class);
Route::model('conferuser', User::class);

Route::middleware(['web'])->group(function () {

Route::any('confer/auth', ['as' => 'confer.pusher.auth', 'uses' => ConversationController::class . '@authenticate']);
Route::get('confer/test', ConversationController::class . '@test');
Route::get('confer/settings', ConversationController::class . '@settings');
Route::get('confer/conversations/bar', ConversationController::class . '@barIndex');
Route::get('confer/conversations', ConversationController::class . '@index');
Route::get('confer/users', ['as' => 'confer.users.list', 'uses' => ConversationController::class . '@listUsers']);
Route::post('confer/user/{conferuser}/info', ['as' => 'confer.user.info', 'uses' => ConversationController::class . '@getUserInfo']);
Route::post('confer/user/{conferuser}/conversation/{conferconversation}/info', ['as' => 'confer.user.conversation.info', 'uses' => ConversationController::class . '@getUserAndConversationInfo']);
Route::get('confer/conversation/{conferconversation}', ['as' => 'confer.conversation.show', 'uses' => ConversationController::class . '@show']);
Route::post('confer/conversation/{conferconversation}/info', ['as' => 'confer.conversation.info', 'uses' => ConversationController::class . '@info']);
Route::post('confer/conversation/{conferconversation}/requested', ['as' => 'confer.conversation.requested', 'uses' => ConversationController::class . '@requested']);
Route::get('confer/conversation/find/user/{conferuser}', ['as' => 'confer.conversation.find', 'uses' => ConversationController::class . '@find']);
Route::delete('confer/conversation/{conferconversation}/leave', ['as' => 'confer.conversation.participant.delete', 'uses' => ConversationController::class . '@leave']);

Route::get('confer/conversation/{conferconversation}/messages', ['as' => 'confer.conversation.messages.show', 'uses' => ConversationController::class . '@showMoreMessages']);
Route::post('confer/conversation/{conferconversation}/messages', ['as' => 'confer.conversation.message.store', 'uses' => MessageController::class . '@store']);

Route::get('confer/conversation/{conferconversation}/invite', ['as' => 'confer.conversation.invite.show', 'uses' => ConversationController::class . '@showInvite']);
Route::patch('confer/conversation/{conferconversation}', ['as' => 'confer.conversation.update', 'uses' => ConversationController::class . '@update']);

Route::post('confer/session', ['as' => 'confer.session.store', 'uses' => SessionController::class . '@store']);
Route::patch('confer/requests/session', ['as' => 'confer.session.update', 'uses' => SessionController::class . '@update']);
Route::get('confer/session/clear', ['as' => 'confer.session.destroy', 'uses' => SessionController::class . '@destroy']);

});

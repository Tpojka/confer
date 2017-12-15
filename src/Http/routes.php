<?php

Route::model('conferconversation', 'Tpojka\Confer\Conversation');
Route::model('conferuser', 'App\User');

Route::any('confer/auth', ['as' => 'confer.pusher.auth', 'uses' => 'Tpojka\Confer\Http\Controllers\ConversationController@authenticate']);
Route::get('confer/test', 'Tpojka\Confer\Http\Controllers\ConversationController@test');
Route::get('confer/settings', 'Tpojka\Confer\Http\Controllers\ConversationController@settings');
Route::get('confer/conversations/bar', 'Tpojka\Confer\Http\Controllers\ConversationController@barIndex');
Route::get('confer/conversations', 'Tpojka\Confer\Http\Controllers\ConversationController@index');
Route::get('confer/users', ['as' => 'confer.users.list', 'uses' => 'Tpojka\Confer\Http\Controllers\ConversationController@listUsers']);
Route::post('confer/user/{conferuser}/info', ['as' => 'confer.user.info', 'uses' => 'Tpojka\Confer\Http\Controllers\ConversationController@getUserInfo']);
Route::post('confer/user/{conferuser}/conversation/{conferconversation}/info', ['as' => 'confer.user.conversation.info', 'uses' => 'Tpojka\Confer\Http\Controllers\ConversationController@getUserAndConversationInfo']);
Route::get('confer/conversation/{conferconversation}', ['as' => 'confer.conversation.show', 'uses' => 'Tpojka\Confer\Http\Controllers\ConversationController@show']);
Route::post('confer/conversation/{conferconversation}/info', ['as' => 'confer.conversation.info', 'uses' => 'Tpojka\Confer\Http\Controllers\ConversationController@info']);
Route::post('confer/conversation/{conferconversation}/requested', ['as' => 'confer.conversation.requested', 'uses' => 'Tpojka\Confer\Http\Controllers\ConversationController@requested']);
Route::get('confer/conversation/find/user/{conferuser}', ['as' => 'confer.conversation.find', 'uses' => 'Tpojka\Confer\Http\Controllers\ConversationController@find']);
Route::delete('confer/conversation/{conferconversation}/leave', ['as' => 'confer.conversation.participant.delete', 'uses' => 'Tpojka\Confer\Http\Controllers\ConversationController@leave']);

Route::get('confer/conversation/{conferconversation}/messages', ['as' => 'confer.conversation.messages.show', 'uses' => 'Tpojka\Confer\Http\Controllers\ConversationController@showMoreMessages']);
Route::post('confer/conversation/{conferconversation}/messages', ['as' => 'confer.conversation.message.store', 'uses' => 'Tpojka\Confer\Http\Controllers\MessageController@store']);

Route::get('confer/conversation/{conferconversation}/invite', ['as' => 'confer.conversation.invite.show', 'uses' => 'Tpojka\Confer\Http\Controllers\ConversationController@showInvite']);
Route::patch('confer/conversation/{conferconversation}', ['as' => 'confer.conversation.update', 'uses' => 'Tpojka\Confer\Http\Controllers\ConversationController@update']);

Route::post('confer/session', ['as' => 'confer.session.store', 'uses' => 'Tpojka\Confer\Http\Controllers\SessionController@store']);
Route::patch('confer/requests/session', ['as' => 'confer.session.update', 'uses' => 'Tpojka\Confer\Http\Controllers\SessionController@update']);
Route::get('confer/session/clear', ['as' => 'confer.session.destroy', 'uses' => 'Tpojka\Confer\Http\Controllers\SessionController@destroy']);
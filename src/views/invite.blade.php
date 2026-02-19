<h2>Who do you want to invite?</h2>
<small>Add from the list of users below to invite to your conversation.</small>

<form method="POST" action="{{ route('confer.conversation.update', $conversation->id) }}" class="confer-invite-form">
	@csrf
@if ($conversation->isPrivate() && ! $potential_invitees->isEmpty())
<small>You'll also want to give your conversation a new name!</small>

<div class="confer-rename-conversation">
	<!--<label for="conversation_name">Name</label>-->
	<input type="text" name="conversation_name" placeholder="Call it something snazzy">
</div>
@endif

<ul class="confer-invite-user-list">
@if ($potential_invitees->isEmpty())
<p>Well this is awkward... you seem to have already invited everyone.</p>
@endif
@foreach ($potential_invitees as $user)

	<li data-userId="{{ $user->id }}">
		<img class="confer-user-avatar" src="{{ url('/') . config('confer.avatar_dir') . $user->avatar }}">
		<span>{{ $user->name }}</span>
		<i class="fa fa-check"></i>
	</li>

@endforeach
</ul>

<button class="confer-button confer-invite-back-button">Back to the conversation</button>
@if ( ! $potential_invitees->isEmpty())
	<button type="submit" class="confer-button confer-button-success confer-invite-save">Invite and update the conversation</button>
@endif
</form>

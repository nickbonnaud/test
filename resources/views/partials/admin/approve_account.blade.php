@if(auth()->user()->is_admin)
  <form method="POST" action="{{ route('review.updateAccount', ['accounts' => $account->slug]) }}">
    {{ method_field('PATCH') }}
		{{ csrf_field() }}
		<input type="hidden" name="status" value="pending">
    <input type="submit" value="Approve" class="btn btn-block btn-success btn-sm">
  </form>
@endif
@if(auth()->user()->is_admin)
  <form method="POST" action="{{ route('review.updateProfile', ['profiles' => $profile->slug]) }}">
    {{ method_field('PATCH') }}
		{{ csrf_field() }}
		<input type="hidden" name="approved" value="true">
    <input type="submit" value="Approve" class="btn btn-block btn-success btn-sm">
  </form>
@endif
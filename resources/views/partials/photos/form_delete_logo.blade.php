<form action="{{ route('photos.deleteWeb', ['profiles' => $profile->slug]) }}" method="POST">
  {{ csrf_field() }}
  <input type="hidden" name="type" value="logo">
  <input type="hidden" name="_method" value="DELETE">
  <button type="submit" class="delete-logo-button"><b>Delete</b> Logo</button>
</form>
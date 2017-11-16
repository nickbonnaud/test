<p><label>Click or Drag-n-Drop your Logo Photo Here</label></p>
<form id="uploadLogo" action="{{ route('photos.storeWeb', ['profiles' => $profile->slug]) }}" method="POST" class="dropzone">
	{{ csrf_field() }}
	<input type="hidden" name="type" value="logo">
</form>
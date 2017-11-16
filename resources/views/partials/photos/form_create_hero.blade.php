<p><label>Click or Drag-n-Drop your Background Image Here</label></p>
 <form id="uploadHero" action="{{ route('photos.storeWeb', ['profiles' => $profile->slug]) }}" method="POST" class="dropzone">
  	{{ csrf_field() }}
  	<input type="hidden" name="type" value="hero">
 </form>
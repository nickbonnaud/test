<div class="form-group">
	<form method="POST" action="{{ route('tags.update', ['profiles' => $profile->slug]) }}" class="form-horizontal">
		{{ method_field('PATCH') }}
		{{ csrf_field() }}
		<label for="tags" class="modal-tags-form-label">Tags</label>
		<select name="tags[]" id="tags" class="form-control" multiple="multiple" style="width: 80%;" required>
			@foreach ($tags as $tag)
	  		<option value="{{ $tag->id }}" {{ in_array($tag->id, $profile->tag_list) ? ' selected="selected"' : '' }}>{{ $tag->name }}</option>
	  	@endforeach
	  </select>
	  <div class="modal-footer modal-footer-form-tags">
	    <div class="form-group">
	      <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
	      <button type="submit" class="btn btn-primary btn-form-footer">Save</button>
	    </div>
	  </div>
	</form>
</div>
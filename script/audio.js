function audio_init(text) {

	// add delete window confirmation
	jQuery(".audio_delete").click(function (e) {

		r = confirm(text);

		// abort submit
		if (!r) {
			e.preventDefault();
		}

	});
}

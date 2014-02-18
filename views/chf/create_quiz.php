<!DOCTYPE html>
<html>
<head>
  <meta http-equiv="content-type" content="text/html; charset=utf-8">
  <script type='text/javascript'src='/js/jquery.1.8.0.min.js'></script>
  <script src="/js/bootstrap.js"></script>
</head>
<body>
	<form action="/chf/web_create_quiz" method='post'>
		<div>
			<label>quiz description</label>
			<textarea name='description'></textarea>
		</div>
		<div>
			<label>quiz title</label>
			<input type='text' name='title' />
		</div>
		<p>
			<label>quiz duration(left empty for no time limitation! if there is time limitation, measured by minites!)</label>
		</p>
			<input type='text' name='time' />
		<div>
			<label>question list</label>
			<div class='question_list'>
				<div>
					<h6>Q1:</h6>
					<div>
						<label>question name?</label>
						<textarea name='question_text[]'></textarea>
					</div>
					<?php
					for ($i = 0; $i < 5; $i++) { ?>
						<div>
							<label>answer <?=$i?>: </label>
							<textarea name='answer_text[]'></textarea>
								<label>Y</label>
								<input type='checkbox' value='1' name='correct[]' />
								<label>N</label>
								<input type='checkbox' value='0' name='correct[]' checked/>
						</div>
					<?php
					} ?>

				</div>
			</div>
		</div>
		<a class='add_question' href='javascript:void(0)'>add a question</a>
			<div></div>
		<div>
			<input type='submit' value='submit' />
		</div>
	</form>

	<script type="text/javascript">
		var cur = 1;
		$('.add_question').click(function() {
			cur += 1;
			var q = "<div> \
					<h6>Q"+cur+":</h6> \
					<div> \
						<label>question name?</label> \
						<textarea name='question_text[]'></textarea> \
					</div>";
			for (i = 0; i < 5; i = i + 1) {
				q = q + "<div> \
								<label>answer "+i+": </label> \
								<textarea name='answer_text[]'></textarea> \
									<label>Y</label> \
									<input type='checkbox' value='1' name='correct[]' /> \
									<label>N</label> \
									<input type='checkbox' value='0' name='correct[]' checked/> \
							</div>"
			}
			$('.question_list').append(q);
		});
	</script>
</body>
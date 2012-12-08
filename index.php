<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
<!-- 		<meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
		<meta name="description" content="">
		<meta name="author" content="">

		<title>ISBT Backstage</title>

		<!-- Le styles -->
		<link href="assets/css/bootstrap.css" rel="stylesheet">
		<link href="assets/css/main.css" rel="stylesheet">

		<!-- Le fav and touch icons -->
		<link rel="shortcut icon" href="assets/favicon.ico">
	</head>

	<body>
	<!--     Le Header     -->
		<div class="header">
			<div class="navbar">
				<div class="navbar-inner">
					<div class="container-fluid">
						<a class="brand" href="#"><b>ISBT</b> Backstage</a>
					</div>
				</div>
			</div>
		</div>

	<!--     Le Navigation     -->
		<div class="navigation">
			<div class="container-fluid">
				<div class="tabbable tabs-below">
					<ul class="nav nav-tabs">
						<li><a href="#match" data-toggle="tab">Match scores</a></li>
						<li><a href="#poule-information" data-toggle="tab">Poule information</a></li>
						<li><a href="#court-information" data-toggle="tab">Court information</a></li>
						<li><a href="#participants" data-toggle="tab">Participants</a></li>
					</ul>
				</div>
			</div>
		</div>

	<!--     Le Body     -->
		<div class="body">
			<div class="container-fluid">
				<div class="row-fluid">
					<div class="span10">
					<div class="alert">
  <button type="button" class="close" data-dismiss="alert">×</button>
  <strong>Warning!</strong> Best check yo self, you're not looking too good.
</div>
						<table class="table table-hover table-striped matches-table">
							<tr>
								<th>
									#
								</th>
								<th>
									Team 1
								</th>
								<th>
									Team 2
								</th>
								<th>
									Poule
								</th>
								<th>
									Round
								</th>
								<th>
									Status
								</th>
								<th>
									Score
								</th>
								<th>
									Comments
								</th>
							</tr>
							<tr data-toggle="modal" data-target="#MatchResults-1">
								<td>
									1
								</td>
								<td>
									Sanne Willems
								</td>
								<td>
									Annelies Smout
								</td>
								<td>
									<span class="label label-warning">Women Single</span> <b>C</b>
								</td>
								<td>
									2
								</td>
								<td>
									<button class="btn btn-small btn-success disabled">Ended</button>
								</td>
								<td>
									1 -1
								</td>
								<td>
									&nbsp;
								</td>
							</tr>
							<tr data-toggle="modal" data-target="#MatchResults-5">
								<td>
									1
								</td>
								<td>
									Wouter van Dijk
								</td>
								<td>
									Frederik Leenders
								</td>
								<td>
									<span class="label label-success">Men Single</span> <b>A</b>
								</td>
								<td>
									1
								</td>
								<td>
									<button class="btn btn-small btn-warning disabled">Ended, awaiting score</button>
								</td>
								<td>
									&nbsp;
								</td>
								<td>
									&nbsp;
								</td>
							</tr>
							<tr data-toggle="modal" data-target="#MatchResults-5">
								<td>
									1
								</td>
								<td>
									Stan van den Bosch<br>
									Rene van Dorland
								</td>
								<td>
									Sylvain de Clerc<br>
									Anton van Rooij
								</td>
								<td>
									<span class="label label-important">Men Double</span> <b>B</b>
								</td>
								<td>
									6
								</td>
								<td>
									<button class="btn btn-small btn-info disabled">In progress...</button>
								</td>
								<td>
									&nbsp;
								</td>
								<td>
									&nbsp;
								</td>
							</tr>
							<tr data-toggle="modal" data-target="#MatchResults-5">
								<td>
									8
								</td>
								<td>
									Nick Scheffelaar<br>
									Linsey van Reep
								</td>
								<td>
									Rob van Neunen<br>
									Annemarie Baars
								</td>
								<td>
									<span class="label label-info">Mixed Double</span> <b>F</b>
								</td>
								<td>
									6
								</td>
								<td>
									<button class="btn btn-small disabled">In cue to start</button>
								</td>
								<td>
									&nbsp;
								</td>
								<td>
									Frederik is gek!
								</td>
							</tr>
							<tr data-toggle="modal" data-target="#MatchResults-5">
								<td>
									8
								</td>
								<td>
									Katrien Duck<br>
									Zwarte Magica
								</td>
								<td>
									Oma Duck<br>
									De grote boze wolf
								</td>
								<td>
									<span class="label label-inverse">Women Double</span> <b>B</b>
								</td>
								<td>
									6
								</td>
								<td>
									<button class="btn btn-small disabled">In cue to start</button>
								</td>
								<td>
									&nbsp;
								</td>
								<td>
									&nbsp;
								</td>
							</tr>									
						</table>
					</div>
					<div class="span2 well">
						<form>
							<input class="span12" type="text" placeholder="Search…">
						</form>
						<p>Hier kan nog een menu komen, suggesties?</p>
						<br>
						<button class="span12 btn btn-primary" type="button">Start next round</button>
					</div>
				</div>
			</div>
		</div>
	
	<!--     Le Footer     -->
	
	<!--     Le Modals     -->
	<div id="MatchResults-1" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="AddMatchResults" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3 id="AddMatchResults">Add match results</h3>
		</div>
		<div class="modal-body">
			<table class="table">
				<tr>
					<th>#1</th>
					<th>Court 2</th>
					<th><span class="label label-success">Men single</span> <b>B</b></th>
				</tr>
				<tr>
					<td>Anton van Rooij</td>
					<td>vs</td>
					<td>Wouter van Dijk</td>
				</tr>
			</table>
			<form class="form-inline">
				<label><b>Results</b></label><br />
				<table class="table modal-table-results">
					<tr>
						<td>
							<div class="input-prepend">
								<span class="add-on">1e set</span><input class="input-small" id="prependedInput" size="16" type="text" placeholder="21 - 15" data-mask="99-99">
							</div>
						</td>
						<td>
							<div class="input-prepend">
								<span class="add-on">2e set</span><input class="input-small" id="prependedInput" size="16" type="text" placeholder="9 - 21" data-mask="99-99">
							</div>
						</td>
					</tr>
				</table>
		</div>
		<div class="modal-footer">
				<button class="btn" data-dismiss="modal">Close</button>
				<button class="btn btn-primary" type="submit">Save changes</button>
			</form>
		</div>
	</div>

	<div id="MatchResults-2" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="AddMatchResults" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3 id="AddMatchResults">Add match results</h3>
		</div>
		<div class="modal-body">
			<table class="table">
				<tr>
					<th>#1</th>
					<th>Court 2</th>
					<th><span class="label label-success">Men single</span> <b>B</b></th>
				</tr>
				<tr>
					<td>Anton van Rooij</td>
					<td>vs</td>
					<td>Wouter van Dijk</td>
				</tr>
			</table>
			<form class="form-inline">
				<label><b>Results</b></label><br />
				<table class="table modal-table-results">
					<tr>
						<td>
							<div class="input-prepend">
								<span class="add-on">1e set</span><input class="input-small" id="prependedInput" size="16" type="text" placeholder="__ - __" data-mask="99-99">
							</div>
						</td>
						<td>
							<div class="input-prepend">
								<span class="add-on">2e set</span><input class="input-small" id="prependedInput" size="16" type="text" placeholder="__ - __" data-mask="99-99">
							</div>
						</td>
						<td>
							<div class="input-prepend">
								<span class="add-on">3e set</span><input class="input-small" id="prependedInput" size="16" type="text" placeholder="__ - __" data-mask="99-99">
							</div>
						</td>
					</tr>
				</table>
		</div>
		<div class="modal-footer">
				<button class="btn" data-dismiss="modal">Close</button>
				<button class="btn btn-primary" type="submit">Save changes</button>
			</form>
		</div>
	</div>

	<div id="MatchResults-3" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="AddMatchResults" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3 id="AddMatchResults">Add match results</h3>
		</div>
		<div class="modal-body">
			<table class="table">
				<tr>
					<th>#1</th>
					<th>Court 2</th>
					<th><span class="label label-success">Men single</span> <b>B</b></th>
				</tr>
				<tr>
					<td>Anton van Rooij</td>
					<td>vs</td>
					<td>Wouter van Dijk</td>
				</tr>
			</table>
			<form class="form-inline">
				<label><b>Results</b></label><br />
				<table class="table modal-table-results">
					<tr>
						<td>
							<div class="input-prepend">
								<span class="add-on">1e set</span><input class="input-small" id="prependedInput" size="16" type="text" placeholder="__ - __" data-mask="99-99">
							</div>
						</td>
						<td>
							<div class="input-prepend">
								<span class="add-on">2e set</span><input class="input-small" id="prependedInput" size="16" type="text" placeholder="__ - __" data-mask="99-99">
							</div>
						</td>
						<td>
							<div class="input-prepend">
								<span class="add-on">3e set</span><input class="input-small" id="prependedInput" size="16" type="text" placeholder="__ - __" data-mask="99-99">
							</div>
						</td>
					</tr>
				</table>
		</div>
		<div class="modal-footer">
				<button class="btn" data-dismiss="modal">Close</button>
				<button class="btn btn-primary" type="submit">Save changes</button>
			</form>
		</div>
	</div>

	<div id="MatchResults-4" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="AddMatchResults" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3 id="AddMatchResults">Add match results</h3>
		</div>
		<div class="modal-body">
			<table class="table">
				<tr>
					<th>#1</th>
					<th>Court 2</th>
					<th><span class="label label-success">Men single</span> <b>B</b></th>
				</tr>
				<tr>
					<td>Anton van Rooij</td>
					<td>vs</td>
					<td>Wouter van Dijk</td>
				</tr>
			</table>
			<form class="form-inline">
				<label><b>Results</b></label><br />
				<table class="table modal-table-results">
					<tr>
						<td>
							<div class="input-prepend">
								<span class="add-on">1e set</span><input class="input-small" id="prependedInput" size="16" type="text" placeholder="__ - __" data-mask="99-99">
							</div>
						</td>
						<td>
							<div class="input-prepend">
								<span class="add-on">2e set</span><input class="input-small" id="prependedInput" size="16" type="text" placeholder="__ - __" data-mask="99-99">
							</div>
						</td>
						<td>
							<div class="input-prepend">
								<span class="add-on">3e set</span><input class="input-small" id="prependedInput" size="16" type="text" placeholder="__ - __" data-mask="99-99">
							</div>
						</td>
					</tr>
				</table>
		</div>
		<div class="modal-footer">
				<button class="btn" data-dismiss="modal">Close</button>
				<button class="btn btn-primary" type="submit">Save changes</button>
			</form>
		</div>
	</div>

	<div id="MatchResults-5" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="AddMatchResults" aria-hidden="true">
		<div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
			<h3 id="AddMatchResults">Add match results</h3>
		</div>
		<div class="modal-body">
			<table class="table">
				<tr>
					<th>#1</th>
					<th>Court 2</th>
					<th><span class="label label-success">Men single</span> <b>B</b></th>
				</tr>
				<tr>
					<td>Anton van Rooij</td>
					<td>vs</td>
					<td>Wouter van Dijk</td>
				</tr>
			</table>
			<form class="form-inline">
				<label><b>Results</b></label><br />
				<table class="table modal-table-results">
					<tr>
						<td>
							<div class="input-prepend">
								<span class="add-on">1e set</span><input class="input-small" id="prependedInput" size="16" type="text" placeholder="__ - __" data-mask="99-99">
							</div>
						</td>
						<td>
							<div class="input-prepend">
								<span class="add-on">2e set</span><input class="input-small" id="prependedInput" size="16" type="text" placeholder="__ - __" data-mask="99-99">
							</div>
						</td>
						<td>
							<div class="input-prepend">
								<span class="add-on">3e set</span><input class="input-small" id="prependedInput" size="16" type="text" placeholder="__ - __" data-mask="99-99">
							</div>
						</td>
					</tr>
				</table>
		</div>
		<div class="modal-footer">
				<button class="btn" data-dismiss="modal">Close</button>
				<button class="btn btn-primary" type="submit">Save changes</button>
			</form>
		</div>
	</div>

	<!-- Le javascript -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script>
		<script src="assets/js/bootstrap.min.js"></script>
		<script src="assets/js/main.js"></script>

	</body>

</html>
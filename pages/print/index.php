<?php
	if(isset($_GET["poule"]) == true && is_numeric($_GET["poule"]) == true)
	{
		require_once('../../php/classes/class.initialiseISBT.php');
		new initialiseISBT();
		$matches = pouleInformationController::getMatches($_GET["poule"]);
		
		if(count($matches) < 1)
		{
			echo 'no matches found!';
			exit;
		}
	}
	else
	{
		echo 'you\'ll be punished for making up this URL!!';
		exit;
	}
?>

<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
<!-- 		<meta name="viewport" content="width=device-width, initial-scale=1.0"> -->
		<meta name="description" content="">
		<meta name="author" content="">

		<title>ISBT Backstage</title>

		<!-- Le styles -->
		<link href="../../assets/css/bootstrap.min.css" rel="stylesheet" media="all">
		<link href="../../assets/css/main.css" rel="stylesheet" media="screen">
		<link href="../../assets/css/print.css" rel="stylesheet" media="print">

		<!-- Le fav and touch icons -->
		<link rel="shortcut icon" href="../../assets/favicon.ico">
	</head>

	<body>
		<div class="game-notes">
			<?php foreach($matches as $match): ?>
			<table class="table table-bordered">
				<tr>
					<th rowspan="2"><h5>Court:</h5></th>
					<th colspan="2">Match number #<?php echo $match['id']; ?></th>
					<th colspan="2"><button class="btn btn-small btn-warning disabled"> Round <?php echo $match['round']; ?></button></th>
					<th colspan="2"><?php echo Toolbox::getCategoryLabel($match['category']); ?></th>
				</tr>
				<tr>
					<td colspan="3"><?php echo $match['team1_user1']?>  +  <?php echo $match['team1_user2']?></td>
					<td colspan="3"><?php echo $match['team2_user1']?>  +  <?php echo $match['team2_user2']?></td>
				</tr>
				<tr>
					<td>
						1<sup>st</sup> set
					</td>
					<td colspan="3">
					</td>
					<td colspan="3">
					</td>
				</tr>
				<tr>
					<td>
						2<sup>nd</sup> set
					</td>
					<td colspan="3">
					</td>
					<td colspan="3">
					</td>
				</tr>
				<tr>
					<td>
						3<sup>th</sup> set
					</td>
					<td colspan="3">
					</td>
					<td colspan="3">
					</td>
				</tr>
			</table>
			<?php endforeach; ?>
		</div>
		<!-- Le javascript -->
		<!-- Placed at the end of the document so the pages load faster -->
		<script src="../../assets/js/jquery.min.js"></script>
		<script src="../../assets/js/bootstrap.min.js"></script>
		<script src="../../assets/js/bootstrap-inputmask.js"></script>
		<script src="../../assets/js/main.js"></script>
		<script type="text/javascript">
			$(document).ready(function()
			{
				window.print();
				return false;
			});
		</script>
	</body>
</html>
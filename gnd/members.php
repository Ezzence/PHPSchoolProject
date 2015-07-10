<?php
include_once('index.php');
include_once('connectDB.php');


if($stmt = $mysqli->prepare("SELECT GNDTeam.id, GNDTeam.name, text, textEng, pic, Account.name FROM GNDTeam 
INNER JOIN Account ON accountid = Account.id ORDER BY GNDTeam.id ASC")){
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $name, $text, $textEn, $pic, $author);
	$stmt->fetch();
	$rowMax = $stmt->num_rows;
	echo '<section>
	<header>
	<h2 hidden> Members </h2>
	</header>';
	for($iter = 0; $iter < $rowMax; ++$iter){
		echo '<article>
		<header>
		<h3 hidden> entry </h3>
		</header>';
		echo '<br><figure>
		<img src="'. $pic . '"style="width: 17.5%; height: 25%;"/>
		</figure>';
		if($language == 'hu'){
			echo '<br>' . $name . 
			'<br><br>' . $text . '<br><br>';
		}else{
			echo '<br>' . $name . 
			'<br><br>' . $textEn . '<br><br>';
		}
		echo '<footer>
		</footer><hr>
		</article>';
		$stmt->fetch();
	}
	echo '</section>';
}else{
	echo 'DATABASE ERROR';
	$_SESSION['msg'] = 'DATABASE ERROR';
}

echo '</body>
	</html>';
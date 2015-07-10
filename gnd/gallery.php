<?php
include_once('index.php');
include_once('connectDB.php');

if(!isset($_GET['index'])){
	$_GET['index'] = 0;
}else{
	if($_GET['index'] < 0){
		header('Location: /gnd/gallery.php?index=0');
	}
}

if($stmt = $mysqli->prepare("SELECT GNDGallery.id, title, titleEng, link, text, textEng, addDate, pic, name FROM GNDGallery 
INNER JOIN Account ON accountid = Account.id ORDER BY GNDGallery.id DESC")){
	$stmt->execute();
	$stmt->store_result();
	$stmt->bind_result($id, $title, $titleEn, $link, $text, $textEn, $date, $pic, $author);
	$stmt->fetch();
	$rowMax = $stmt->num_rows;
	echo '<section>
	<header>
	<h2 hidden> Galleries </h2>
	</header>';
	for($iter = 0; $iter < $rowMax; ++$iter){
		if($_GET['index'] > $rowMax){
			$_GET['index'] -= 15;
		}
		if($iter < ($_GET['index'] + 15) && ($iter >= $_GET['index'])){
			echo '<article>
			<header>
			<h3 hidden> entry </h3>
			</header>';
			echo '<br><figure>
			<a href="' . $link . '"><img src="'. $pic . '" style="width: 17.5%; height: 25%;" /></a>
			</figure>';
			if($language == 'hu'){
				echo '<br><a href="' . $link . '">' . $title . '</a> 
				<br><br>' . $text . '<br><br>';
			}else{
				echo '<br><a href="' . $link . '">' . $titleEn . '</a> 
				<br><br>' . $textEn . '<br><br>';
			}
			echo '<footer>' . $date . ' by ' . $author .
			'</footer><hr>
			</article>';
		}
		$stmt->fetch();
	}
	echo '</section>';
}else{
	echo 'DATABASE ERROR';
	$_SESSION['msg'] = 'DATABASE ERROR';
}
echo '<a href="' . htmlspecialchars($_SERVER['PHP_SELF']) . '?index=' . ($_GET['index'] - 15) . '">prev</a> ';
echo '<a href="' . htmlspecialchars($_SERVER['PHP_SELF']) . '?index=' . ($_GET['index'] + 15) . '">next</a>';

echo '</body>
	</html>';
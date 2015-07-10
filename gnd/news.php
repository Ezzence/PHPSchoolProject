<?php
include_once('index.php');
include_once('connectDB.php');

if(!isset($_GET['index'])){
	$_GET['index'] = 0;
}else{
	if($_GET['index'] < 0){
		header('Location: /gnd/news.php?index=0');
	}
}
if(isset($_GET['article'])){
	if($stmt = $mysqli->prepare("SELECTT title, titleEng, text, textEng, addDate, name FROM GNDdocs INNER JOIN Account 
	ON accountid = Account.id WHERE GNDdocs.id = ? ORDER BY GNDDocs.id DESC")){
		$stmt->bind_param('i', $_GET['article']);
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($title, $titleEn, $text, $textEn, $date, $author);
		$stmt->fetch();
		if($stmt->num_rows < 1){
			echo '<br> Article not found <br>';
		}else{
			echo '<section>
			<header>
			<h2 hidden> Articles </h2>
			</header>';
			echo '<article>
			<header>
			<h3 hidden> entry </h3>
			</header>';
			if($language == 'hu'){
				echo '<br>' . $title . '<br><br>' . $text . '<br><br>';
			}else{
				echo '<br>' . $titleEn . '<br><br>' . $textEn . '<br><br>';
			}
			echo '<footer>' . $date . ' by ' . $author . 
			'</footer><hr>
			</article>';
			if(login_check($mysqli) && $_SESSION['newsPriv'] == true){
				echo '<a href="/gnd/priv/edit_news.php?article=' . $_GET['article'] . '">edit</a>';
			}
			echo '</section>';
		}
	}else{
		echo 'DATABASE ERROR';
		$_SESSION['msg'] = 'DATABASE ERROR';
		header()
	}
}else{

	if(isset($_SESSION['msg'])){
		echo $_SESSION['msg'];
	}
	if($stmt = $mysqli->prepare("SELECT GNDdocs.id, title, titleEng, text, textEng, addDate, pic, revealDate, name FROM GNDdocs 
	INNER JOIN Account ON accountid = Account.id ORDER BY GNDDocs.id DESC")){
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($id, $title, $titleEn, $text, $textEn, $date, $pic, $revealDate, $author);
		$stmt->fetch();
		$rowMax = $stmt->num_rows;
		echo '<section>
		<header>
		<h2 hidden> Articles </h2>
		</header>';
		for($iter = 0; $iter < $rowMax; ++$iter){
			if($_GET['index'] > $rowMax){
				$_GET['index'] -= $indexer;
			}
			$curdate = (string)date('Y-m-d');
			if($iter < ($_GET['index'] + $indexer) && ($iter >= $_GET['index']) && ($curdate >= $revealDate)){
				echo '<article>
				<header>
				<h3 hidden> entry </h3>
				</header>';
				echo '<br><figure>
				<a href="' . htmlspecialchars($_SERVER['PHP_SELF']) . '?article=' . $id . '"><img src="'. $pic . '"
				style="width: 17.5%; height: 25%;"/></a>
				</figure>';
				if($language == 'hu'){
					$ending = '...';
					if(strlen($text) < 200){
						$ending = ' ';
					}
					echo '<br><a href="' . htmlspecialchars($_SERVER['PHP_SELF']) . '?article=' . $id . '">' . $title . '</a> 
					<br><br>' . substr($text, 0, 199) . $ending . '<br><br>';
				}else{
					$ending = '...';
					if(strlen($textEn) < 200){
						$ending = ' ';
					}
					echo '<br><a href="' . htmlspecialchars($_SERVER['PHP_SELF']) . '?article=' . $id . '">' . $titleEn . '</a> 
					<br><br>' . substr($textEn, 0, 199) . $ending . '<br><br>';
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
	echo '<a href="' . htmlspecialchars($_SERVER['PHP_SELF']) . '?index=' . ($_GET['index'] - $indexer) . '">prev</a>';
	for($i = 0; $i < $rowMax; $i+=$indexer){
		if($i != $_GET['index']){
			echo '<a href="' . htmlspecialchars($_SERVER['PHP_SELF']) . '?index=' . $i . '"> ' . ($i/$indexer) . ' </a>';
		}
	}
	echo ' <a href="' . htmlspecialchars($_SERVER['PHP_SELF']) . '?index=' . ($_GET['index'] + $indexer) . '">next</a>';
}

echo '</body>
	</html>';
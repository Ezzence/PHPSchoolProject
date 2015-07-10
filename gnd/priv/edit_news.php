<?php
include_once('../index.php');
include_once('../connectDB.php');

if(login_check($mysqli) && $_SESSION['newsPriv'] == true){
	
	if(isset($_POST['editNews'])){
		echo '<br><br>';
		echo  $_POST['newsTitleAreaHu'];
		echo '<br><br>';
		echo  $_POST['newsTextAreaHu'];
		echo '<br><br>';
		echo  $_POST['newsTitleAreaEn'];
		echo '<br><br>';
		echo  $_POST['newsTextAreaEn'];
		
		if($_FILES['pictureUpload']['error']==0) {
			if ($stmt = $mysqli->prepare("UPDATE GNDDocs SET title = ?, titleEng = ?, text = ?, textEng = ?, pic = ?
		WHERE id = ?")) {
				$pic = '/gnd/pic/' .  basename($_FILES["pictureUpload"]["name"]);
				$stmt->bind_param('sssssi', $_POST['newsTitleAreaHu'], $_POST['newsTitleAreaEn'], 
				$_POST['newsTextAreaHu'], $_POST['newsTextAreaEn'], $pic, $_POST['article']);
				$stmt->execute();
			}else{
				echo 'DATABASE ERROR';
				$_SESSION['msg'] = 'DATABASE ERROR';
			}
		}else{
			if ($stmt = $mysqli->prepare("UPDATE GNDDocs SET title = ?, titleEng = ?, text = ?, textEng = ? 
			WHERE id = ?")) {

				$stmt->bind_param('ssssi', $_POST['newsTitleAreaHu'], $_POST['newsTitleAreaEn'], 
				$_POST['newsTextAreaHu'], $_POST['newsTextAreaEn'], $_POST['article']);
				$stmt->execute();
			}else{
				echo 'DATABASE ERROR';
				$_SESSION['msg'] = 'DATABASE ERROR';
			}
		}
	}
	
	echo '<br><a href="/gnd/priv/add_news.php">add news</a><br><br> ';
	
	if(isset($_GET['article'])){
		if(isset($_GET['delete'])){
			if($stmt = $mysqli->prepare("DELETE FROM GNDDocs WHERE GNDdocs.id = ?")){
			$stmt->bind_param('i', $_GET['article']);
			$stmt->execute();
			$_SESSION['msg'] = $lang['DELETE_SUCCESS'];
			header('Location: ' . htmlspecialchars($_SERVER['PHP_SELF']));
			}else{
				echo 'DATABASE ERROR';
				$_SESSION['msg'] = 'DATABASE ERROR';
			}
		}
		if($stmt = $mysqli->prepare("SELECT title, titleEng, text, textEng, addDate, name FROM GNDdocs INNER JOIN Account 
		ON accountid = Account.id WHERE GNDdocs.id = ? ORDER BY GNDdocs.id DESC")){
			$stmt->bind_param('i', $_GET['article']);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($title, $titleEn, $text, $textEn, $date, $author);
			$stmt->fetch();
			if($stmt->num_rows < 1){
				echo '<br> Article not found <br>';
			}else{
				
				echo '<br><div id="newsEditor">
				<script type="text/javascript" src="http://js.nicedit.com/nicEdit-latest.js"></script> <script type="text/javascript">
				 bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
				 </script><br><br>
				 <form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF']) . '" enctype="multipart/form-data">
				 Cím (magyar):
				 <textarea name="newsTitleAreaHu" style="width: 30%; height: 30px;">'
				   . $title .
				'</textarea>
				<br>Hír szövege (magyar):
				<textarea name="newsTextAreaHu" style="width: 50%; height: 300px;">'
				   . $text .
				'</textarea>
				<br><br>Title (in english):
				<textarea name="newsTitleAreaEn" style="width: 30%; height: 30px;">'
				   . $titleEn .
				'</textarea>
				<br>News Article (in english):
				<textarea name="newsTextAreaEn" style="width: 50%; height: 300px;">'
				   . $textEn .
				'</textarea>
				<input hidden type="text" name = "article" value ="' . $_GET['article'] . '"/>
				<br>Kép: <input type="file" name="pictureUpload" id="pictureUpload">
				<br><br><button type="submit" name="editNews" value="edit" > edit </button>
				</form>
				</div>';
				echo '<br><a href="' . htmlspecialchars($_SERVER['PHP_SELF']) . '?article=' . $_GET['article'] . 
				'&delete=true">delete</a>';
			}
				
		}else{
			echo 'DATABASE ERROR';
			$_SESSION['msg'] = 'DATABASE ERROR';
		}
	}else{
		if($stmt = $mysqli->prepare("SELECT GNDdocs.id, title, titleEng, text, textEng, addDate, name FROM GNDdocs INNER JOIN Account 
		ON accountid = Account.id ORDER BY GNDdocs.id DESC")){
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($id, $title, $titleEn, $text, $textEn, $date, $author);
			$stmt->fetch();
			$rowMax = $stmt->num_rows;
			for($iter = 0; $iter < $rowMax; ++$iter){
				if($language == 'hu'){
					echo '<br><a href="' . htmlspecialchars($_SERVER['PHP_SELF']) . '?article=' . $id . '">' . $title . '</a>';
				}else{
					echo '<br><a href="' . htmlspecialchars($_SERVER['PHP_SELF']) . '?article=' . $id . '">' . $titleEn . '</a>';
				}
				echo $date . ' by ' . $author;
				$stmt->fetch();
			}
		}else{
			echo 'DATABASE ERROR';
			$_SESSION['msg'] = 'DATABASE ERROR';
		}
	}
}else{
	echo "NOT ALLOWED";
}
echo '</body>
	</html>';
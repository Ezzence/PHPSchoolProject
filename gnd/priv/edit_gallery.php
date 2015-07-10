<?php
include_once('../index.php');
include_once('../connectDB.php');

if(login_check($mysqli) && $_SESSION['galleryPriv'] == true){
	
	if(isset($_POST['editGallery'])){
		echo '<br><br>';
		echo  $_POST['galleryLinkArea'];
		echo '<br><br>';
		echo  $_POST['galleryTitleAreaHu'];
		echo '<br><br>';
		echo  $_POST['galleryTextAreaHu'];
		echo '<br><br>';
		echo  $_POST['galleryTitleAreaEn'];
		echo '<br><br>';
		echo  $_POST['galleryTextAreaEn'];
		
		if($_FILES['pictureUpload']['error']==0) {
			if ($stmt = $mysqli->prepare("UPDATE GNDGallery SET title = ?, titleEng = ?, link = ?, text = ?, textEng = ?, pic = ?
			WHERE id = ?")) {
				$pic = '/gnd/pic/' .  basename($_FILES["pictureUpload"]["name"]);
				$stmt->bind_param('ssssssi', $_POST['galleryTitleAreaHu'], $_POST['galleryTitleAreaEn'], 
				$_POST['galleryLinkArea'], $_POST['galleryTextAreaHu'], $_POST['galleryTextAreaEn'], $pic, $_POST['article']);
				$stmt->execute();
			}else{
				echo 'DATABASE ERROR';
				$_SESSION['msg'] = 'DATABASE ERROR';
			}
		}else{
			if ($stmt = $mysqli->prepare("UPDATE GNDGallery SET title = ?, titleEng = ?, link = ?, text = ?, textEng = ? 
			WHERE id = ?")) {

				$stmt->bind_param('sssssi', $_POST['galleryTitleAreaHu'], $_POST['galleryTitleAreaEn'], 
				$_POST['galleryLinkArea'], $_POST['galleryTextAreaHu'], $_POST['galleryTextAreaEn'], $_POST['article']);
				$stmt->execute();
			}else{
				echo 'DATABASE ERROR';
				$_SESSION['msg'] = 'DATABASE ERROR';
			}
		}
	}
	
	echo '<br><a href="/gnd/priv/add_gallery.php">add gallery</a><br><br> ';
	
	if(isset($_GET['article'])){
		if(isset($_GET['delete'])){
			if($stmt = $mysqli->prepare("DELETE FROM GNDGallery WHERE GNDGallery.id = ?")){
			$stmt->bind_param('i', $_GET['article']);
			$stmt->execute();
			$_SESSION['msg'] = $lang['DELETE_SUCCESS'];
			header('Location: ' . htmlspecialchars($_SERVER['PHP_SELF']));
			}else{
				echo 'DATABASE ERROR';
				$_SESSION['msg'] = 'DATABASE ERROR';
			}
		}
		if($stmt = $mysqli->prepare("SELECT title, titleEng, link, text, textEng, addDate, pic, name FROM GNDGallery INNER JOIN Account 
		ON accountid = Account.id WHERE GNDGallery.id = ? ORDER BY GNDGallery.id DESC")){
			$stmt->bind_param('i', $_GET['article']);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($title, $titleEn, $link, $text, $textEn, $date, $pic, $author);
			$stmt->fetch();
			if($stmt->num_rows < 1){
				echo '<br> Gallery not found <br>';
			}else{
				
				echo '<br><div id="galleryEditor">
				<script type="text/javascript" src="http://js.nicedit.com/nicEdit-latest.js"></script> <script type="text/javascript">
				bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
				</script><br><br>
				<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF']) . '" enctype="multipart/form-data">
				Link:
				<textarea name="galleryLinkArea" style="width: 40%; height: 30px;">'
				. $link .
				'</textarea>
				<br>Cím (magyar):
				<textarea name="galleryTitleAreaHu" style="width: 30%; height: 30px;">'
				. $title .
				'</textarea>
				<br>Képek Jellemzése (magyar):
				<textarea name="galleryTextAreaHu" style="width: 50%; height: 300px;">'
				. $text .
				'</textarea>
				<br><br>Title (in english):
				<textarea name="galleryTitleAreaEn" style="width: 30%; height: 30px;">'
				. $titleEn .
				'</textarea>
				<br>Gallery Description (in english):
				<textarea name="galleryTextAreaEn" style="width: 50%; height: 300px;">'
				. $textEn .
				'</textarea>
				<input hidden type="text" name = "article" value ="' . $_GET['article'] . '"/>
				<br>Kép: <input type="file" name="pictureUpload" id="pictureUpload">
				<br><br><input type="submit" name="editGallery" value="edit" />
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
		if($stmt = $mysqli->prepare("SELECT GNDGallery.id, title, titleEng, link, text, textEng, addDate, pic, name 
		FROM GNDGallery INNER JOIN Account ON accountid = Account.id ORDER BY GNDGallery.id DESC")){
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($id, $title, $titleEn, $link, $text, $textEn, $date, $pic, $author);
			$stmt->fetch();
			$rowMax = $stmt->num_rows;
			for($iter = 0; $iter < $rowMax; ++$iter){
				if($language == 'hu'){
					echo '<br><a href="' . htmlspecialchars($_SERVER['PHP_SELF']) . '?article=' . $id . '">' . $title . '</a> ';
				}else{
					echo '<br><a href="' . htmlspecialchars($_SERVER['PHP_SELF']) . '?article=' . $id . '">' . $titleEn . '</a> ';
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
<?php
include_once('../index.php');
include_once('../connectDB.php');

if(login_check($mysqli) && $_SESSION['galleryPriv'] == true){
	
	if(isset($_POST['addGallery'])){
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
			if ($stmt = $mysqli->prepare("INSERT INTO GNDGallery (accountid, title, titleEng, link, text, textEng, 
			addDate, pic) VALUES (?, ?, ?, ?, ?, ? , CURDATE(), ?)")) {
				$pic = '/gnd/pic/' .  basename($_FILES["pictureUpload"]["name"]);
				$stmt->bind_param('issssss', $_SESSION['userid'], $_POST['galleryTitleAreaHu'], $_POST['galleryTitleAreaEn'], 
				$_POST['galleryLinkArea'], $_POST['galleryTextAreaHu'], $_POST['galleryTextAreaEn'], $pic);
				$stmt->execute();
			}else{
				echo 'DATABASE ERROR';
				$_SESSION['msg'] = 'DATABASE ERROR';
			}
		}else{
			if ($stmt = $mysqli->prepare("INSERT INTO GNDGallery (accountid, title, titleEng, link, text, textEng, 
			addDate, pic) VALUES (?, ?, ?, ?, ?, ? , CURDATE(), '/gnd/pic/default.jpg')")) {
				$stmt->bind_param('isssss', $_SESSION['userid'], $_POST['galleryTitleAreaHu'], $_POST['galleryTitleAreaEn'], 
				$_POST['galleryLinkArea'], $_POST['galleryTextAreaHu'], $_POST['galleryTextAreaEn']);
				$stmt->execute();
			}else{
				echo 'DATABASE ERROR';
				$_SESSION['msg'] = 'DATABASE ERROR';
			}
		}
	}
	
	echo '<br><div id="galleryEditor">
	<script type="text/javascript" src="http://js.nicedit.com/nicEdit-latest.js"></script> <script type="text/javascript">
	bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
	</script><br><br>
	<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF']) . '" enctype="multipart/form-data">
	Link:
	<textarea name="galleryLinkArea" style="width: 40%; height: 30px;">
	http://www.valami.valami
	</textarea>
	<br>Cím (magyar):
	<textarea name="galleryTitleAreaHu" style="width: 30%; height: 30px;">
	Cím
	</textarea>
	<br>Képek Jellemzése (magyar):
	<textarea name="galleryTextAreaHu" style="width: 50%; height: 300px;">
    Szöveg
	</textarea>
	<br><br>Title (in english):
	<textarea name="galleryTitleAreaEn" style="width: 30%; height: 30px;">
    Title not available in english
	</textarea>
	<br>Gallery Description (in english):
	<textarea name="galleryTextAreaEn" style="width: 50%; height: 300px;">
    Text not available in english
	</textarea>
	<br>Kép: <input type="file" name="pictureUpload" id="pictureUpload">
	<br><br><input type="submit" name="addGallery" value="submit" />
	</form>
	</div>';
}else{
	echo "NOT ALLOWED";
}
echo '</body>
	</html>';
<?php
include_once('../index.php');
include_once('../connectDB.php');

if(login_check($mysqli) && $_SESSION['sponsorPriv'] == true){
	
	if(isset($_POST['editSponsors'])){
		echo '<br><br>';
		echo  $_POST['sponsorsLinkArea'];
		echo '<br><br>';
		echo  $_POST['sponsorsNameArea'];
		echo '<br><br>';
		echo  $_POST['sponsorsTextAreaHu'];
		echo '<br><br>';
		echo  $_POST['sponsorsTextAreaEn'];
		
		if($_FILES['pictureUpload']['error']==0) {
			if ($stmt = $mysqli->prepare("UPDATE GNDSponsor SET name = ?, text = ?, textEng = ?, link = ?, pic = ? WHERE id = ?")) {
				$pic = '/gnd/pic/' .  basename($_FILES["pictureUpload"]["name"]);
				$stmt->bind_param('sssssi', $_POST['sponsorsNameArea'], $_POST['sponsorsTextAreaHu'], 
				$_POST['sponsorsTextAreaEn'], $_POST['sponsorsLinkArea'], $pic, $_POST['article']);
				$stmt->execute();
			}else{
				echo 'DATABASE ERROR';
				$_SESSION['msg'] = 'DATABASE ERROR';
			}
		}else{
			if ($stmt = $mysqli->prepare("UPDATE GNDSponsor SET name = ?, text = ?, textEng = ?, link = ? WHERE id = ?")) {
				$stmt->bind_param('ssssi', $_POST['sponsorsNameArea'], $_POST['sponsorsTextAreaHu'], 
				$_POST['sponsorsTextAreaEn'], $_POST['sponsorsLinkArea'], $_POST['article']);
				$stmt->execute();
			}else{
				echo 'DATABASE ERROR';
				$_SESSION['msg'] = 'DATABASE ERROR';
			}
		}
	}
	
	echo '<br><a href="/gnd/priv/add_sponsors.php">add sponsors</a><br><br> ';
	
	if(isset($_GET['article'])){
		if(isset($_GET['delete'])){
			if($stmt = $mysqli->prepare("DELETE FROM GNDSponsor WHERE GNDSponsor.id = ?")){
			$stmt->bind_param('i', $_GET['article']);
			$stmt->execute();
			$_SESSION['msg'] = $lang['DELETE_SUCCESS'];
			header('Location: ' . htmlspecialchars($_SERVER['PHP_SELF']));
			}else{
				echo 'DATABASE ERROR';
				$_SESSION['msg'] = 'DATABASE ERROR';
			}
		}
		if($stmt = $mysqli->prepare("SELECT GNDSponsor.name, text, textEng, link, pic, Account.name FROM GNDSponsor 
		INNER JOIN Account ON accountid = Account.id WHERE GNDSponsor.id = ? ORDER BY GNDSponsor.id DESC")){
			$stmt->bind_param('i', $_GET['article']);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($name, $text, $textEn, $link, $pic, $author);
			$stmt->fetch();
			if($stmt->num_rows < 1){
				echo '<br> Name not found <br>';
			}else{
				
				echo '<br><div id="sponsorsEditor">
				<script type="text/javascript" src="http://js.nicedit.com/nicEdit-latest.js"></script> <script type="text/javascript">
				 bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
				</script><br><br>
				<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF']) . '" enctype="multipart/form-data">
				Link:
				<textarea name="sponsorsLinkArea" style="width: 40%; height: 30px;">'
				. $link .
				'</textarea>
				<br>Név (name):
				<textarea name="sponsorsNameArea" style="width: 30%; height: 30px;">'
				. $name .
				'</textarea>
				<br>Leírás (magyar):
				<textarea name="sponsorsTextAreaHu" style="width: 50%; height: 300px;">'
				. $text .
				'</textarea>
				<br><br>Description (in english):
				<textarea name="sponsorsTextAreaEn" style="width: 50%; height: 300px;">'
				. $textEn .
				'</textarea>
				<input hidden type="text" name = "article" value ="' . $_GET['article'] . '"/>
				<br>Kép: <input type="file" name="pictureUpload" id="pictureUpload">
				<br><br><input type="submit" name="editSponsors" value="edit" />
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
		if($stmt = $mysqli->prepare("SELECT GNDSponsor.id, GNDSponsor.name, text, textEng, link, pic, Account.name FROM GNDSponsor 
		INNER JOIN Account ON accountid = Account.id ORDER BY GNDSponsor.id DESC")){
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($id, $name, $text, $textEn, $link, $pic, $author);
			$stmt->fetch();
			$rowMax = $stmt->num_rows;
			for($iter = 0; $iter < $rowMax; ++$iter){
				if($language == 'hu'){
					echo '<br><a href="' . htmlspecialchars($_SERVER['PHP_SELF']) . '?article=' . $id . '">' . $name . '</a>';
				}else{
					echo '<br><a href="' . htmlspecialchars($_SERVER['PHP_SELF']) . '?article=' . $id . '">' . $name . '</a>';
				}
				echo ' added by ' . $author;
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
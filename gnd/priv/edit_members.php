<?php
include_once('../index.php');
include_once('../connectDB.php');

if(login_check($mysqli) && $_SESSION['teamPriv'] == true){
	
	if(isset($_POST['editMembers'])){
		echo '<br><br>';
		echo  $_POST['membersNameArea'];
		echo '<br><br>';
		echo  $_POST['membersTextAreaHu'];
		echo '<br><br>';
		echo  $_POST['membersTextAreaEn'];
		
		if($_FILES['pictureUpload']['error']==0) {
			if ($stmt = $mysqli->prepare("UPDATE GNDTeam SET name = ?, text = ?, textEng = ?, pic = ? WHERE id = ?")) {
				$pic = '/gnd/pic/' .  basename($_FILES["pictureUpload"]["name"]);
				$stmt->bind_param('ssssi', $_POST['membersNameArea'], $_POST['membersTextAreaHu'], 
				$_POST['membersTextAreaEn'], $pic, $_POST['article']);
				$stmt->execute();
			}else{
				echo 'DATABASE ERROR';
				$_SESSION['msg'] = 'DATABASE ERROR';
			}
			
		}else{
			if ($stmt = $mysqli->prepare("UPDATE GNDTeam SET name = ?, text = ?, textEng = ? WHERE id = ?")) {
				$stmt->bind_param('sssi', $_POST['membersNameArea'], $_POST['membersTextAreaHu'], 
				$_POST['membersTextAreaEn'], $_POST['article']);
				$stmt->execute();
			}else{
				echo 'DATABASE ERROR';
				$_SESSION['msg'] = 'DATABASE ERROR';
			}
		}
	}
	
	echo '<br><a href="/gnd/priv/add_members.php">add members</a><br><br> ';
	
	if(isset($_GET['article'])){
		if(isset($_GET['delete'])){
			if($stmt = $mysqli->prepare("DELETE FROM GNDTeam WHERE GNDTeam.id = ?")){
			$stmt->bind_param('i', $_GET['article']);
			$stmt->execute();
			$_SESSION['msg'] = $lang['DELETE_SUCCESS'];
			header('Location: ' . htmlspecialchars($_SERVER['PHP_SELF']));
			}else{
				echo 'DATABASE ERROR';
				$_SESSION['msg'] = 'DATABASE ERROR';
			}
		}
		if($stmt = $mysqli->prepare("SELECT GNDTeam.name, text, textEng, pic, Account.name FROM GNDTeam 
		INNER JOIN Account ON accountid = Account.id WHERE GNDTeam.id = ? ORDER BY GNDTeam.id DESC")){
			$stmt->bind_param('i', $_GET['article']);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($name, $text, $textEn, $pic, $author);
			$stmt->fetch();
			if($stmt->num_rows < 1){
				echo '<br> Name not found <br>';
			}else{
				
				echo '<br><div id="membersEditor">
				<script type="text/javascript" src="http://js.nicedit.com/nicEdit-latest.js"></script> <script type="text/javascript">
				 bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
				</script><br><br>
				<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF']) . '" enctype="multipart/form-data">
				Név (name):
				<textarea name="membersNameArea" style="width: 30%; height: 30px;">'
				. $name .
				'</textarea>
				<br>Leírás (magyar):
				<textarea name="membersTextAreaHu" style="width: 50%; height: 300px;">'
				   . $text .
				'</textarea>
				<br><br>Description (in english):
				<textarea name="membersTextAreaEn" style="width: 50%; height: 300px;">'
				   . $textEn .
				'</textarea>
				<input hidden type="text" name = "article" value ="' . $_GET['article'] . '"/>
				<br>Kép: <input type="file" name="pictureUpload" id="pictureUpload">
				<br><br><input type="submit" name="editMembers" value="edit" />
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
		if($stmt = $mysqli->prepare("SELECT GNDTeam.id, GNDTeam.name, text, textEng, pic, Account.name FROM GNDTeam 
		INNER JOIN Account ON accountid = Account.id ORDER BY GNDTeam.id DESC")){
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($id, $name, $text, $textEn, $pic, $author);
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
<?php
include_once('../index.php');
include_once('../connectDB.php');

if(login_check($mysqli) && $_SESSION['menuPriv'] == true){
	
	if(isset($_POST['editMenu'])){
		echo '<br><br>';
		echo  $_POST['menuNameAreaHu'];
		echo '<br><br>';
		echo  $_POST['menuNameAreaEn'];
		echo '<br><br>';
		echo  $_POST['menuTextAreaHu'];
		echo '<br><br>';
		echo  $_POST['menuTextAreaEn'];
		if ($stmt = $mysqli->prepare("UPDATE GNDMenu SET name = ?, nameEng = ?, text = ?, textEng = ? WHERE id = ?")) {
			$stmt->bind_param('ssssi', $_POST['menuNameAreaHu'],  $_POST['menuNameAreaEn'], $_POST['menuTextAreaHu'], 
			$_POST['menuTextAreaEn'], $_POST['article']);
			$stmt->execute();
		}else{
			echo 'DATABASE ERROR';
			$_SESSION['msg'] = 'DATABASE ERROR';
		}
	}
	
	echo '<br><a href="/gnd/priv/add_menu.php">add menu item</a><br><br> ';
	
	if(isset($_GET['article'])){
		if(isset($_GET['delete'])){
			if($stmt = $mysqli->prepare("DELETE FROM GNDMenu WHERE GNDMenu.id = ?")){
			$stmt->bind_param('i', $_GET['article']);
			$stmt->execute();
			$_SESSION['msg'] = $lang['DELETE_SUCCESS'];
			header('Location: ' . htmlspecialchars($_SERVER['PHP_SELF']));
			}else{
				echo 'DATABASE ERROR';
				$_SESSION['msg'] = 'DATABASE ERROR';
			}
		}
		if($stmt = $mysqli->prepare("SELECT GNDMenu.name, nameEng, text, textEng FROM GNDMenu 
		WHERE GNDMenu.id = ? ORDER BY GNDMenu.id DESC")){
			$stmt->bind_param('i', $_GET['article']);
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($name, $nameEn, $text, $textEn);
			$stmt->fetch();
			if($stmt->num_rows < 1){
				echo '<br> Name not found <br>';
			}else{
				
				echo '<br><div id="menuEditor">
				<script type="text/javascript" src="http://js.nicedit.com/nicEdit-latest.js"></script> <script type="text/javascript">
				 bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
				</script><br><br>
				<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF']) . '">
				Név (magyar):
				<textarea name="menuNameAreaHu" style="width: 30%; height: 30px;">'
				. $name .
				'</textarea>
				Name (english):
				<textarea name="menuNameAreaEn" style="width: 30%; height: 30px;">'
				. $nameEn .
				'</textarea>
				<br>Szöveg (magyar):
				<textarea name="menuTextAreaHu" style="width: 50%; height: 300px;">'
				   . $text .
				'</textarea>
				<br><br>Text (in english):
				<textarea name="menuTextAreaEn" style="width: 50%; height: 300px;">'
				   . $textEn .
				'</textarea>
				<input hidden type="text" name = "article" value ="' . $_GET['article'] . '"/>
				<input type="submit" name="editMenu" value="edit" />
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
		if($stmt = $mysqli->prepare("SELECT GNDMenu.id, GNDMenu.name, nameEng, text, textEng FROM GNDMenu
		ORDER BY GNDMenu.id DESC")){
			$stmt->execute();
			$stmt->store_result();
			$stmt->bind_result($id, $name, $nameEn, $text, $textEn);
			$stmt->fetch();
			$rowMax = $stmt->num_rows;
			for($iter = 0; $iter < $rowMax; ++$iter){
				if($language == 'hu'){
					echo '<br><a href="' . htmlspecialchars($_SERVER['PHP_SELF']) . '?article=' . $id . '">' . $name . '</a>';
				}else{
					echo '<br><a href="' . htmlspecialchars($_SERVER['PHP_SELF']) . '?article=' . $id . '">' . $nameEn . '</a>';
				}
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
<?php
include_once('../index.php');
include_once('../connectDB.php');

if(login_check($mysqli) && $_SESSION['menuPriv'] == true){
	
	if(isset($_POST['addMenu'])){
		echo '<br><br>';
		echo  $_POST['menuNameAreaHu'];
		echo '<br><br>';
		echo  $_POST['menuNameAreaEn'];
		echo '<br><br>';
		echo  $_POST['menuTextAreaHu'];
		echo '<br><br>';
		echo  $_POST['menuTextAreaEn'];
		if ($stmt = $mysqli->prepare("INSERT INTO GNDMenu (name, nameEng, text, textEng) 
		VALUES (?, ?, ?, ?)")) {
			$stmt->bind_param('ssss', $_POST['menuNameAreaHu'],
			$_POST['menuNameAreaEn'], $_POST['menuTextAreaHu'], $_POST['menuTextAreaEn']);
			$stmt->execute();
		}else{
			echo 'DATABASE ERROR';
			$_SESSION['msg'] = 'DATABASE ERROR';
		}
	}
	
	echo '<br><div id="menuEditor">
	<script type="text/javascript" src="http://js.nicedit.com/nicEdit-latest.js"></script> <script type="text/javascript">
	 bkLib.onDomLoaded(function() { nicEditors.allTextAreas() });
	</script><br><br>
	<form method="post" action="' . htmlspecialchars($_SERVER['PHP_SELF']) . '">
	<br>Név (magyar):
	<textarea name="menuNameAreaHu" style="width: 30%; height: 30px;">
	</textarea>
	Name (english):
	<textarea name="menuNameAreaEn" style="width: 30%; height: 30px;">
	</textarea>
	<br>Szöveg (magyar):
	<textarea name="menuTextAreaHu" style="width: 50%; height: 300px;">
    Szöveg
	</textarea>
	<br>Text (english):
	<textarea name="menuTextAreaEn" style="width: 50%; height: 300px;">
    Text not available in english
	</textarea>
	<input type="submit" name="addMenu" value="submit" />
	</form>
	</div>';
}else{
	echo "NOT ALLOWED";
}
echo '</body>
	</html>';
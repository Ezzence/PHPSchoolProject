<?php
include_once('../index.php');
include_once('../connectDB.php');

if(login_check($mysqli) && $_SESSION['filePriv'] == true){
	
	echo '<br><a href="/gnd/priv/add_files.php">add files</a><br><br> ';
	
	if($stmt = $mysqli->prepare("SELECT GNDFiles.id, GNDFiles.addDate, file, Account.name FROM GNDFiles
	INNER JOIN Account ON accountid = Account.id ORDER BY GNDFiles.id DESC")){
		$stmt->execute();
		$stmt->store_result();
		$stmt->bind_result($id, $date, $file, $author);
		$stmt->fetch();
		$rowMax = $stmt->num_rows;
		for($iter = 0; $iter < $rowMax; ++$iter){
			echo '<br><a href="/gnd/priv/download.php?download_file=' . $file . '">' . $file . '</a> ';
			echo  $date . ' added by ' . $author;
			$stmt->fetch();
		}
	}else{
		echo 'DATABASE ERROR';
		$_SESSION['msg'] = 'DATABASE ERROR';
	}
}else{
	echo "NOT ALLOWED";
}
echo '</body>
	</html>';
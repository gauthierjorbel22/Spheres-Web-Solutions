<?php
	$pdo = new PDO('mysql:host=localhost;port=3306;dbname=spheresITSP','l3gitAdmin', '@dmin$3cret');
	$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>
<?php
$database = new Database(array(
	'username' => 'root',
	'password' => '',
	'database' => '',
	'hostname' =>'localhost'
));

$users = $database->run("SELECT * FROM `users`")->fetchAll();
foreach($users as $user) {
    echo 'Username: ' . $user['username'];
}

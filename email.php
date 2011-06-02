<?php
require_once "Mail.php";

$from = "turboasm@gmail.com";
$to = "turboasm@gmail.com";
$subject = "Hola";
$body = "Esto es una prueba";

$host = "alex.rugby@gmail.com";
$username = "mi usuario";
$password = "mi password";

$headers = array ('From' => $from,
'To' => $to,
'Subject' => $subject);
$smtp = Mail::factory('smtp',
array ('host' => $host,
'auth' => true,
'username' => $username,
'password' => $password));

$mail = $smtp->send($to, $headers, $body);

if (PEAR::isError($mail)) {
echo("<p>" . $mail->getMessage() . "</p>");
} else {
echo("<p>Message successfully sent!</p>");
}
?>

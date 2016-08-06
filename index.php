<?php
require_once 'CPU.php';
@session_start();

$cpu = new CPU();

if (isset($_POST['user_word']) && $_POST['user_word'] !== '') {
	$user_word = $_POST['user_word'];
	$cpu_word = $cpu->reply($user_word);
	$_SESSION['cpu_endWord'] = $cpu->saveStrEnd($cpu_word);
} else if (isset($_POST['user_word']) && $_POST['user_word'] === '') {
	$cpu_word = $cpu->surrenderReply();
	if (isset($_SESSION['cpu_endWord'])) {
		unset($_SESSION['cpu_endWord']);
	}
}

?>
<!doctype html>
<html lang="ja">
<head>
	<meta charset="utf-8">
	<title>Siritori</title>
</head>
<body>

<h1>最弱しりとりプログラム</h1>

<ul>
	<li>入力する言葉はカタカナかひらがな3文字でなくてはならず、それ以外はユーザの敗北とみなす</li>
	<li>ユーザ、CPUともに、同じ言葉を何度使ってもオーケイ</li>
	<li>最初の文字がなにではじまる単語がCPUが返せないか当てるゲーム</li>
	<li>ユーザが返す言葉は当然、しりとりになっていなくてはならない</li>
</ul>

<form action="" method="post">
	<input type="text" name="user_word" autofocus>
	<input type="submit" value="言う">
</form>

<?php if (!empty($cpu_word)): ?>
	<p><?=$cpu_word?></p>
<?php endif; ?>

</body>
</html>
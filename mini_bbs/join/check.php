<?php
session_start();
require('../dbconnect.php');

/*
 /$_SESSIONのjoinという配列の内容があるかどうかを判断
 issetはある場合はtrue ない場合はfalseを返す

 $_SESSIONのjoinに内容が入ってない場合にifを実行
 入力画面を正しく通過せずcheck.phpが呼び出される場合
 強制的にindex/phpに戻す
*/
if(!isset($_SESSION['join'])){
	header('Location: index.php');
	exit();
}

if(!empty($_POST)){
//データベースへの登録
	$statement = $db->prepare('INSERT INTO members SET name=?, email=?, password=?, picture=?, created=NOW()');
	echo $statement->execute(array(
		 $_SESSION['join']['name'],
		 $_SESSION['join']['email'],
		 sha1($_SESSION['join']['password']),
		 $_SESSION['join']['image']
	));
	unset($_SESSION['join']);

	header('Location: thanks.php');
	exit();
}

?>

<!DOCTYPE html>
<html lang="ja">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<title>会員登録</title>

	<link rel="stylesheet" href="../style.css" />
</head>
<body>
<div id="wrap">
<div id="head">
<h1>会員登録</h1>
</div>

<div id="content">
<p>記入した内容を確認して、「登録する」ボタンをクリックしてください</p>
<form action="" method="post">
	<input type="hidden" name="action" value="submit" />
	<dl>
		<dt>ニックネーム</dt>
		<!-- 出力する内容は$_SESSIONのjoinのname -->
		<dd>
		<?php echo htmlspecialchars($_SESSION['join']['name'], ENT_QUOTES); ?>
        </dd>
		<dt>メールアドレス</dt>
		<dd>
		<?php echo htmlspecialchars($_SESSION['join']['email'], ENT_QUOTES); ?>
        </dd>
		<dt>パスワード</dt>
		<dd>
		【表示されません】
		</dd>
		<dt>写真など</dt>
		<dd>
		<!-- $_SESSIONのjoinのimageが空ではなければimgタグを出力する member_pictureの中のimageを呼び出し、出力する -->
		<?php if($_SESSION['join']['image'] !== ''): ?>
			<img src="../member_picture/<?php echo htmlspecialchars($_SESSION['join']['image'], ENT_QUOTES); ?>">
<?php endif ?>
		</dd>
	</dl>
	<div><a href="index.php?action=rewrite">&laquo;&nbsp;書き直す</a> | <input type="submit" value="登録する" /></div>
</form>
</div>

</div>
</body>
</html>

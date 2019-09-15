<?php
session_start();
require('../dbconnect.php');

/*
入力内容を確認するボタンを押したときだけ実行したいが、
phpではボタンの判断はできない　またEnterKeyでもフォームが送信できる
そこで、フォームが送信したかどうかを確認する必要がある

フォームが送信したかどうか = 配列が空かどうか = 空ではない場合はエラーチェックを走らせる
*/
	if(!empty($_POST)){

//ニックネームが空であるかどうかを確認 $errorは配列
	if($_POST['name'] === ''){
		$error['name'] = 'blank';
	}
	if($_POST['email'] === ''){
		$error['email'] = 'blank';
	}
	//パスワードの長さを指定(4文字以下でエラー文)
	if(strlen($_POST['password']) < 4){
		$error['password'] = 'length';
	}
	if($_POST['password'] === ''){
		$error['password'] = 'blank';
	}
	//imageというname属性がついたファイルアップロードのコントロールからアップロードされたファイル名
	//何度か使うので$fileNameという変数に保管
	$fileName = $_FILES['image']['name'];
	//画像がアップロードされている場合
	if(!empty($fileName)){
		//ファイル名の末尾3文字を切り取っている(拡張子) それを$extに保管
		$ext = substr($fileName, -3);
		//拡張子がjpgでもgifでもpngでもない場合　画像エラーをtypeというエラーにする
		if($ext != 'jpg' && $ext != 'gif' && $ext != 'png'){
			$error['image'] = 'type';
		}
	}
	
	/*アカウントの重複をチェック
	データベースから入力したメールアドレスのアカウントを取得し、
	それが0以上である場合はデータベースに登録されている*/
	if(empty($error)){
		/*COUNT(*)で件数が何件かを取得し、cntというショートカット名に格納、
		それをmembersテーブルから取得、WHERE email=?で絞り込み*/
		$member = $db->prepare('SELECT COUNT(*) AS cnt FROM members WHERE email=?');
		$member->execute(array($_POST['email']));
		$record = $member->fetch();
		if($record['cnt'] > 0){
			$error['email'] = 'duplicate';
		}
	}

	//empty(空)ならばtrueを返す　配列を指定する この場合$error
	if(empty($error)){
		//アップロードするファイル名 例：2019082702617myface.png
		$image = date('YmdHis') . $_FILES['image']['name'];
		//$_FILESというグローバル変数はtype=fileフィールドから得られた内容 配列になっている
		move_uploaded_file($_FILES['image']['tmp_name'],'../member_picture/' .$image);
		//エラーが起こってないことが確認できたときにsessionに値を保存
		$_SESSION['join'] = $_POST;
		//sessionのjoinという配列の中にimageというキーを作り、その中にファイル名を保管
		$_SESSION['join']['image'] = $image;
		//check.phpにジャンプする
		header('Location: check.php');
	exit();
	}

}

//URLパラメータがついている場合とsessionが正しく設定されている場合（書き直し部分）
if($_REQUEST['action'] == 'rewrite' && isset($_SESSION['join'])){
	$_POST = $_SESSION['join'];
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
<p>次のフォームに必要事項をご記入ください。</p>
<!--action属性が空なのはhtmlとしては正しく、空の場合は自分自身のファイルにジャンプする
enctype="multipart/form-data"はファイルのアップロードに必要な記述,決り文句-->
<form action="" method="post" enctype="multipart/form-data">
	<dl>
		<dt>ニックネーム<span class="required">必須</span></dt>
		<dd>
        	<input type="text" name="name" size="35" maxlength="255" value="<?php echo htmlspecialchars($_POST['name'],ENT_QUOTES); ?>" />
			<?php if($error['name'] === 'blank'): ?>
			<p class="error">*ニックネームを入力してください</p>
			<?php endif ?>
		</dd>
		<dt>メールアドレス<span class="required">必須</span></dt>
		<dd>
        	<input type="text" name="email" size="35" maxlength="255" value="<?php echo htmlspecialchars($_POST['email'],ENT_QUOTES); ?>" />
			<?php if($error['email'] === 'blank'): ?>
			<p class="error">*メールアドレスを入力してください</p>
			<?php endif ?>
			<?php if($error['email'] === 'duplicate'): ?>
			<p class="error">*指定されたメールアドレスは既に登録されています</p>
			<?php endif ?>
		<dt>パスワード<span class="required">必須</span></dt>
		<dd>
        	<input type="password" name="password" size="10" maxlength="20" value="<?php echo htmlspecialchars($_POST['password'],ENT_QUOTES); ?>" />
			<?php if($error['password'] === 'length'): ?>
			<p class="error">*パスワードは4文字以上で入力してください</p>
			<?php endif ?>
			<?php if($error['password'] === 'blank'): ?>
			<p class="error">*パスワードを入力してください</p>
			<?php endif ?>
        </dd>
		<dt>写真など</dt>
		<dd>
			<!-- type="file"はファイル選択ウインドウを表示 -->
        	<input type="file" name="image" size="35" value="test"  />
			<!-- エラーのimageがtypeだったら -->
			<?php if($error['image'] === 'type'): ?>
			<p class="error">*写真などは「.gif」または「.jpg」「.png」の画像を指定してください</p>
			<?php endif ?>
			
			<!-- $errorの中に何かが入っていた場合,空ではない場合エラーを表示-->
			<?php if(!empty($error)): ?>
			<p class="error">*恐れ入りますが、画像を改めて指定してください</p>
			<?php endif ?>
        </dd>
	</dl>
	<div><input type="submit" value="入力内容を確認する" /></div>
</form>
</div>
</body>
</html>

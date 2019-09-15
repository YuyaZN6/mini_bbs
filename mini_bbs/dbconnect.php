<?php
//例外処理
try {
    //$db変数にPDOオブジェクトを作成 最初に作ったデータベースを指定 データベースに接続できるようにする
    $db = new PDO('mysql:dbname=mini_bbs;host=127.0.0.1;charset=utf8', 'root', '');
//データベースに正常に接続できなかった場合のcatch処理
} catch (PDOException $e) {
    echo 'DB接続エラー：' . $e->getMessage();
}

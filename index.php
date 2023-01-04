<?php
//----------
// 1行age機能付きbbsサンプル
//----------

//データベース接続PDO
define('DB_PDO', 'sqlite:age.db');

//初期設定
init();

function init() {
  try {
    if (!is_file('age.db')) {
      // はじめての実行ならDB作成
      $db = new PDO(DB_PDO);
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      // ID, 書いた日時, スレ親orレス, 親スレ, コメントID, スレ構造ID, 名前, 本文, age値
      $sql = "CREATE TABLE tlog (tid integer primary key autoincrement, created TIMESTAMP, thread VARCHAR(1), parent INT, comid BIGINT, tree BIGINT, a_name TEXT, com TEXT, age INT)";
			$db = $db->query($sql);
			$db = null; //db切断
    }
  } catch (PDOException $e) {
		echo "DB接続エラー:" . $e->getMessage();
	}
  if (!is_writable(realpath("./"))) error("カレントディレクトリに書けません<br>");
}

//投稿をデータベースへ保存 - スレ建て
function regist() {
  $name = (string)filter_input(INPUT_POST, 'name');
  $com = (string)filter_input(INPUT_POST, 'com');
  try {
    $db = new PDO(DB_PDO);
    if (isset($_POST["send"])) {
      if ($name === "") $name = '名無しさん';
			if ($com === "") $com  = 'コメントなし';
      
      // 'のエスケープ
      $name = str_replace("'", "''", $name);
      $com = str_replace("'", "''", $com);

      // age値取得
      $sqlage = "SELECT MAX(age) FROM tlog";
			$age = $db->exec("$sqlage");
			$tree = time() * 100000000;

			$thread = 1;
			$age = 0;
			$parent = NULL;

      $sql = "INSERT INTO tlog (created, thread, parent, comid, tree, a_name, com, age) VALUES (datetime('now', 'localtime'), '$thread', '$parent', '$tree', '$tree', '$name', '$com', '$age')";
			$db->exec($sql);
    }
  } catch (PDOException $e) {
		echo "DB接続エラー:" . $e->getMessage();
	}
}

//エラー画面
function error($mes)
{
	global $db;
	$db = null; //db切断
	echo $mes;
	exit;
}

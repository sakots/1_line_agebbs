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
      // ID, スレ親orレス, 親スレ, コメントID, スレ構造ID, 名前, 本文
      $db = new PDO(DB_PDO);
			$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $sql = "CREATE TABLE tlog (tid integer primary key autoincrement, thread VARCHAR(1), parent INT, comid BIGINT, tree BIGINT, a_name TEXT, com TEXT)";
			$db = $db->query($sql);
			$db = null; //db切断
    }
  } catch (PDOException $e) {
		echo "DB接続エラー:" . $e->getMessage();
	}
  if (!is_writable(realpath("./"))) error("カレントディレクトリに書けません<br>");
}

//エラー画面
function error($mes)
{
	global $db;
	$db = null; //db切断
	echo $mes;
	exit;
}

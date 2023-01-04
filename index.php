<?php
//----------
// 1行age機能付きbbsサンプル
//----------

//データベース接続PDO
define('DB_PDO', 'sqlite:age.db');
//日付フォーマット
define('DATE_FORMAT', 'Y/m/d H:i:s');

//初期設定
init();

/*-----------mode-------------*/

$mode = filter_input(INPUT_POST, 'mode');

if (filter_input(INPUT_GET, 'mode') === "regist") {
	$mode = "regist";
}
if (filter_input(INPUT_GET, 'mode') === "reply") {
	$mode = "reply";
}

switch ($mode) {
  case 'regist':
    return regist();
  case 'reply':
    return reply();
  default:
    return def();
}
exit;

/*-----------Main-------------*/

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
      $db = null; //db切断
    }
  } catch (PDOException $e) {
		echo "DB接続エラー:" . $e->getMessage();
	}
}

//投稿をデータベースへ保存 - リプライ
function reply() {
  $name = (string)filter_input(INPUT_POST, 'name');
  $com = (string)filter_input(INPUT_POST, 'com');
  $parent = trim(filter_input(INPUT_POST, 'parent', FILTER_VALIDATE_INT));
  try {
    $db = new PDO(DB_PDO);
    if (isset($_POST["send"])) {
      if ($name === "") $name = '名無しさん';
			if ($com === "") $com  = 'コメントなし';

      //最新コメント取得
			$sqlw = "SELECT * FROM tlog WHERE thread=0 ORDER BY tid DESC LIMIT 1";
			$msgw = $db->prepare($sqlw);
			$msgw->execute();
			$msgwc = $msgw->fetch();

      //最初のレスのage処理対策
			$msgwc["tid"] = 0;
			$msgwc["age"] = 0;
			$msgwc["tree"] = 0;
      
      // 'のエスケープ
      $name = str_replace("'", "''", $name);
      $com = str_replace("'", "''", $com);

      // レスの位置
			$tree = time() - $parent - (int)$msgwc["tid"];
			$comid = $tree + time();

			$thread = 0;
			$age = (int)$msgwc["age"];

      $sql = "INSERT INTO tlog (created, thread, parent, comid, tree, a_name, com, age) VALUES (datetime('now', 'localtime'), '$thread', '$parent', '$comid', '$tree', '$name', '$com', '$age')";
			$db->exec($sql);
      $db = null; //db切断
    }
  } catch (PDOException $e) {
		echo "DB接続エラー:" . $e->getMessage();
	}
}

//通常表示モード
function def() {
  try {
    //全スレッド取得
    $db = new PDO(DB_PDO);
    $sql = "SELECT * FROM tlog WHERE invz=0 AND thread=1 ORDER BY tree DESC";
    $posts = $db->query($sql);

		$ko = array();
		$oya = array();

    $i = 0;
		$j = 0;
    while ($i < 100) { //スレッドは100件にしとく
			$bbsline = $posts->fetch();
			if (empty($bbsline)) {
				break;
			} //スレがなくなったら抜ける
			$oid = $bbsline["tid"]; //スレのtid(親番号)を取得
			$sqli = "SELECT * FROM tlog WHERE parent = $oid AND invz=0 AND thread=0 ORDER BY comid ASC";
			//レス取得
			$postsi = $db->query($sqli);
			$j = 0;
			$flag = true;
			while ($flag == true) {
				$res = $postsi->fetch();
				if (empty($res)) { //レスがなくなったら
					$flag = false;
					break;
				} //抜ける
				$res['resno'] = ($j + 1); //レス番号
				
				//日付をUNIX時間に変換して設定どおりにフォーマット
				$res['created'] = date(DATE_FORMAT, strtotime($res['created']));
				$res['modified'] = date(DATE_FORMAT, strtotime($res['modified']));
				$ko[] = $res;
				$j++;
			}
			//日付をUNIX時間にしたあと整形
			$bbsline['created'] = date(DATE_FORMAT, strtotime($bbsline['created']));
			$bbsline['modified'] = date(DATE_FORMAT, strtotime($bbsline['modified']));
			$oya[] = $bbsline;
			$i++;
		}

    $dat['ko'] = $ko;
		$dat['oya'] = $oya;
    $db = null; //db切断
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

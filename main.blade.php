<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>1行BBS</title>
</head>
<body>
  <h1>1行BBS age機能付き</h1>
  <hr>
  <h2>スレッド作成</h2>
  <form action="index.php?mode=regist" method="post" enctype="multipart/form-data">
    <p>
      <label for="a_name">名前：<input type="text" id="a_name" name="name"></label>
      <label for="com">本文：<input type="text" id="com" name="com"></label>
      <input type="submit" id="submit" name="send" value="書き込む">
    </p>
  </form>
  <hr>

</body>
</html>
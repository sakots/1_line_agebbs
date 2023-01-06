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
      <label for="a_name">名前：<input type="text" id="a_name" name="name" size="10"></label>
      <label for="com">本文：<input type="text" id="com" name="com" size="20"></label>
      <input type="submit" id="submit" name="send" value="書き込む">
    </p>
  </form>
  <hr>
  <h2>本文</h2>
  <div>
    @if (!empty($oya))
    @foreach ($oya as $bbsline)
    <section class="thread">
      [{{$bbsline['tid']}}] : {{$bbsline['created']}} - {{$bbsline['a_name']}} : {{$bbsline['com']}}
      @if (!empty($ko))
			@foreach ($ko as $res)
      @if ($bbsline['tid'] === $res['parent'])
        <section>└ [{{$res['tid']}}] : {{$res['created']}} - {{$res['a_name']}} : {{$res['com']}}</section>
      @endif
      @endforeach
      @endif
      <form action="index.php?mode=reply">
        <p>
          <label for="rep_a_name_{{$bbsline['tid']}}">名前：<input type="text" id="rep_a_name_{{$bbsline['tid']}}" name="name" size="10"></label>
          <label for="rep_com_{{$bbsline['tid']}}">本文：<input type="text" id="rep_com_{{$bbsline['tid']}}" name="com" size="20"></label>
          <input type="submit" id="submit" name="send" value="書き込む">
        </p>
      </form>
    </section>
    @endforeach
    @endif
  </div>
</body>
</html>

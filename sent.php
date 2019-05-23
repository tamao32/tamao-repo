<?php
 $sentId = $_GET["sent"];
// タイムゾーン設定します
date_default_timezone_set('Asia/Tokyo');


// 入力エラーメッセージ処理
if(!empty($_POST["submit_re"])){
	if(empty($_POST["re_name"])){
				$error_message[] = "投稿者名を入力してくださいね。";
	}
	if(empty($_POST["re_message"])){
		$error_message[] = "ひと言メッセージを入力してくださいね。";
	}
}
if(empty($_POST["re_name"])){
		$error_message[] = "";
}
if(empty($_POST["re_message"])){
		$error_message[] = "";
}

// データベースの接続
$mysqli = new mysqli('mysql1.php.xdomain.ne.jp', 'ppftech_user1', 'user1234', 'ppftech_db1');

if( empty($error_message) ) {
	if( $mysqli->connect_errno ) {
			$error_message[] = '書き込みに失敗しました。 エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
		} else {

		// 文字コード設定
		$mysqli->set_charset("utf8");

		// 書き込み日時を取得
		$now_date = date("Y-m-d H:i:s");

		// データを登録するSQL作成
		$sql = "INSERT INTO post_tamao (parent_id, name, message, post_datetime)
		VALUES ('".$sentId."','".$_POST['re_name']."', '".$_POST['re_message']."', '$now_date')";

		// データを登録
		$res = $mysqli->query($sql);

		if( $res ) {
				$success_message = 'メッセージを書き込みました。';
        header("Location: sent.php?sent=$sentId", true, 303);
        exit;

			} else {
				$error_message[] = '書き込みに失敗しました。';
			}
			// データベースの接続を閉じる
			$mysqli->close();
	}
}

// データベースに接続
$mysqli = new mysqli('mysql1.php.xdomain.ne.jp', 'ppftech_user1', 'user1234', 'ppftech_db1');
// 接続エラーの確認
if( $mysqli->connect_error ) {
  $error_message[] = 'データの読み込みに失敗しました。 エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
} else {
//select文
	$mysqli->set_charset("utf8");
  $sql = "SELECT id,name,message,post_datetime FROM post_tamao where parent_id = $sentId ORDER BY post_datetime DESC";
  $res = $mysqli->query($sql);
  if( $res ) {
    $post_all_re = array();
    // $row = $res->fetch_assoc();
  }
  while ($row = $res->fetch_assoc()) {
    $post_all_re[] = $row;
    }
  $mysqli->close();
}
?>


<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>ひと言掲示板</title>
  <link rel="stylesheet" type="text/css" href="index.css">
</head>
<body>
  <div class="header">
    <div class="header-left">ひと言掲示板</div>
    <div class="header-right">
      <ul>
        <a href = "index.php">TOP</a>
      </ul>
    </div>
  </div>

  <div class="main">
    <div class="thanks-message">
      <!-- <h3><?php echo $sentId ?>番への返信</h3> -->
      </div>
    <div class="display-contact">
      <h3 class="section-title">返信内容</h3>
      <!-- 入力エラーメッセージ表示 -->
      <?php if(!empty($error_message)):?>
        <ul class = "error_message">
          <?php foreach($error_message as $error): ?>
            <li><?php echo $error; ?></li>
          <?php endforeach;?>
        </ul>
    <?php endif;?>

      <form action = "sent.php?sent=<?php echo $sentId ?>" method = "post">
        <div class="contact-form">
        <p>返信者名（必須）</p>
        <input type = "text" name = "re_name">
        <p>返信メッセージ（必須）</p>
        <textarea name = "re_message" ></textarea>
        <p>※必須項目は必ずご入力ください</p>
        <input  type = "submit" value = "返信" name = "submit_re">

    </div>
  </form>
        <h3 class="section-title">返信内容</h3>

  </div>
  <article>
  <?php if (!empty($post_all_re)){?>

  	<?php foreach($post_all_re as $post_display_re){?>
  		    <div class="info">

  				<h3><time><?php echo date('Y年m月d日 H:i', strtotime($post_display_re['post_datetime'])); ?></time></h3>
  				<h2><?php echo $post_display_re['name']; ?></h2>
  			</div>
  				<p><?php echo $post_display_re['message']; ?></p>
<hr>
  	<?php } ?>
  	<?php } ?>
  </article>
</div>
</body>
</html>

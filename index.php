<?php
$url = $_SERVER["REQUEST_URI"];
// タイムゾーン設定
date_default_timezone_set('Asia/Tokyo');

// 入力エラーメッセージ処理
if(!empty($_POST["submit"])){
	if(empty($_POST["name"])){
		$error_message[] = "投稿者名を入力してくださいね。";
	}
	if(empty($_POST["message"])){
		$error_message[] = "ひと言メッセージを入力してくださいね。";
	}
}

if(empty($_POST["name"])){
	$error_message[] = "";
}

if(empty($_POST["message"])){
	$error_message[] = "";
}

if( empty($error_message) ) {
	// データベースの接続（insert文）
	$mysqli = new mysqli('mysql1.php.xdomain.ne.jp', 'ppftech_user1', 'user1234', 'ppftech_db1');
	if( $mysqli->connect_errno ) {
		$error_message[] = '書き込みに失敗しました。 エラー番号 '.$mysqli->connect_errno.' : '.$mysqli->connect_error;
	} else {
		// 文字コード設定
		$mysqli->set_charset("utf8");
		$mysqli->autocommit(TRUE);
		// 書き込み日時を取得
		$now_date = date("Y-m-d H:i:s");
		// データを登録するSQL作成
		$sql = "INSERT INTO post_tamao (name, message, post_datetime)
		VALUES ('".$_POST['name']."', '".$_POST['message']."', '$now_date')";
		// データを登録
		$res = $mysqli->query($sql);
			if( $res ) {
				header("Location: index.php", true, 303);
				exit;
			} else {
				$error_message[] = '書き込みに失敗しました。';
			}
			// データベースの接続を閉じる
			$mysqli->close();
	}
}

	// データベースに接続(select文)
	$mysqli = new mysqli('mysql1.php.xdomain.ne.jp', 'ppftech_user1', 'user1234', 'ppftech_db1');
	// 接続エラーの確認
	if( $mysqli->connect_errnor ) {
		$error_message[] = 'データの読み込みに失敗しました。 エラー番号 '.$mysqli->connect_errnor.' : '.$mysqli->connect_error;
	}else{
		$mysqli->set_charset("utf8");
	}
		//select文
		$sql = "SELECT id,name,message,post_datetime FROM post_tamao where parent_id = 0  ORDER BY post_datetime DESC ";
		$res = $mysqli->query($sql);
		//データベースを閉じる
		$mysqli->close();

		if( $res ) {
			$post_all = array();
		}
		while ($row = $res->fetch_assoc()) {
			$post_all[] = $row;
		}
?>

<!-- HTML文 -->
<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8">
		<title>ひと言掲示板</title>
		<link rel="stylesheet" type="text/css" href="index.css">
	</head>
	<body>
		<div class="header">
			<div class="header-logo">ひと言掲示板</div>
		<p><?php echo $page_error; ?></p>
      <div class="header-list">
        <ul>
          <li>ひと言掲示板とは</li>
          <li>管理者情報</li>
          <li>お問い合わせ</li>
        </ul>
      </div>
    </div>

		<div class="main">
		 <div class="copy-container">
			 <h1>ひと言掲示板<span>！！</span></h1>
			 <h4>ひと言掲示板の世界へようこそ</h4>
		 </div>
		 	<form action = "index.php" method = "post">
		 		<div class="contact-form">

				<!-- 入力エラーメッセージ表示 -->
				<?php if(!empty($error_message)):?>
					<ul class = "error_message">
						<?php foreach($error_message as $error): ?>
							<li><?php echo $error; ?></li>
						<?php endforeach;?>
					</ul>
			<?php endif;?>
				<!-- 入力エリア -->
	 			<p>投稿者名!!（必須）</p>
	 			<input type = "text" name = "name">
	 			<p>ひと言メッセージ（必須）</p>
				<textarea name = "message" ></textarea>
	 			<p>※必須項目は必ずご入力ください</p>
	 			<input  type = "submit" value = "投稿" name = "submit">
			</div>
		</form>

<!-- 投稿メッセージ表示 -->
<article>
	<h3 class="section-title">投稿内容</h3>
</article>
<!-- ページング機能 -->
<?php
$logdata =$post_all;
$count =  $res->num_rows;//ログの数
$max = 5;
//1ページあたりの表示数
$limit = ceil($count/$max);//最大ページ数
$page = empty($_GET["page"])? 1:$_GET["page"];//ページ番号

function paging($limit, $page, $disp=5){//$dispはページ番号の表示数
	$next = $page+1;
  $prev = $page-1;

  //ページ番号リンク用
	$start =  ($page-floor($disp/2) > 0) ? ($page-floor($disp/2)) : 1;//始点
  $end =  ($start > 1) ? ($page+floor($disp/2)) : $disp;//終点
  $start = ($limit < $end)? $start-($end-$limit):$start;//始点再計算

	if($page != 1 ) {
         print '<a href="?page='.$prev.'">&laquo;前へ&nbsp;</a>';
			 }

    //最初のページへのリンク
		if($start >= floor($disp/2)){
			print '<a href="?page=1">先頭&nbsp;</a>';
      if($start > floor($disp/2)) print "..."; //ドットの表示
		}

		for($i=$start; $i <= $end ; $i++){ //ページリンク表示ループ
			$class = ($page == $i) ? ' class="current"':"";//現在地を表すCSSクラス
			if($i <= $limit && $i > 0 )//1以上最大ページ数以下の場合
			print '<a href="?page='.$i.'"'.$class.'>&nbsp;'.$i.'&nbsp;</a>';//ページ番号リンク表示
		}
		//最後のページへのリンク
		if($limit > $end){
			if($limit-1 > $end ){
			print "...";    //ドットの表示
      print '<a href="?page='.$limit.'">&nbsp;最後&nbsp;</a>';
		}
	}

	if($page < $limit){
		print '<a href="?page='.$next.'">次へ &raquo;</a>';
    }
}

if(is_numeric($page)){
	paging($limit, $page);
}

//不正入力処理
if($page > $limit ||is_numeric($page) == false){
	$page_error = "ページが存在しません";
}

//最大ページ数
$page = empty($_GET["page"])? 1:$_GET["page"];//ページ番号

function disp_log($page,$max){
	global $logdata,$count;
	 $start = ($page == 1)? 0 : ($page-1) * $max;
	 $end = ($page * $max);
	 print "<p>";
	 for($i=$start;$i<$end;$i++ ){
		 if($i >= $count){break;}
?>
			<div class = "info" >
			<p3><?php
				// if($i > 0){
				$a = $i+1;
				if($a == 1){
			 print $a.".最新記事";
		 }else{
			  print $a.".";
		 }
		 		?></p3>
				</div>
			<p3><?php print	date('Y年m月d日 H:i', strtotime($logdata[$i]['post_datetime']))."<br>"; ?></p3>
			<p><?php print $logdata[$i]['name']."<br>"; ?></p>
			<p><?php print $logdata[$i]['message']."<br>"; ?></p>

			<?php foreach($logdata as $post_display){?>
				<?php $post_display = $logdata[$i]['id'] ?>
			<?php } ?>

			<form action="sent.php" method="get">
				<a href = "sent.php?sent=<?php echo $post_display  ?>">返信/返信一覧</a>
			</form>
<?php }
print "</p>";
}

// 整数判定
if(is_numeric($page)){
	disp_log($page,$max);
}
?>
</div>

<!-- ページエラーメッセージ -->
<div class = page_error>
	<p><?php print $page_error; ?></p>
</div>
	</body>
</html>

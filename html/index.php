<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <title>たまごチェッカー</title>
    <link rel="stylesheet" type="text/css" href="index.css">
  </head>
  <body>
     <?php
  // エラーを出力する
    ini_set('display_errors', "On");
    ?>
    <div class="main">
    <h1>たまごチェッカー Ver 0.0.2</h1>
    <p>取りあえず動くものを作りたかっただけなので精度の保証はしません。</p>
    <p>猫かどうかの判定はしてないので、必ず猫の画像を送信してね。</p>
    <h2>画像アップロード</h2>
<!--送信ボタンが押された場合-->
<?php if (isset($_POST['upload'])):
    //echo "処理中... しばらくお待ち下さい";
    $tmp_file = $_FILES['image']['tmp_name'];
    //画像ファイルかのチェック
    if (exif_imagetype($tmp_file)) {
        $message = '';
        $img = base64_encode(file_get_contents($tmp_file));

        $url = 'http://127.0.0.1/api';
        $ch = curl_init($url);

        $POST_DATA = array(
            'image' => $img
        );
        curl_setopt($ch, CURLOPT_POST, TRUE);//POSTで送信
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($POST_DATA));//データをセット
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, 400);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);//受け取ったデータを変数に
        $html = curl_exec($ch);
        if(curl_errno($ch)){
            echo "エラー";
        }

        curl_close($ch);
        echo $html;

    } else {
        $message = '画像ファイルを選択してください';
    }
?>
    <p><?php echo $message; ?></p>
<?php endif;?>
    <form method="post" enctype="multipart/form-data">
        <input type="file" name="image" accept="image/*">
        <button class="button" type="submit" name="upload" >送信</button>
    </form>
    <p>↓これがたまごちゃんだよ</p>
    <img src="tamago.jpg" alt="おっ立つたまご" title="おっ立つたまご" width="80%">

    <script>
    $(function() {
      $('input[type=file]').after('<span></span>');

      // アップロードするファイルを選択
      $('input[type=file]').change(function() {
        var file = $(this).prop('files')[0];

        // 画像以外は処理を停止
        if (! file.type.match('image.*')) {
          // クリア
          $(this).val('');
          $('span').html('');
          return;
        }

        // 新幅・高さ
        var new_w = 200;
        var new_h = 200;

        // 画像表示
        var reader = new FileReader();
        reader.onload = function() {
          var img_src = $('<img>').attr('src', reader.result);

          var org_img = new Image();
          org_img.src = reader.result;
          org_img.onload = function() {
            // 元幅・高さ
            var org_w = this.width;
            var org_h = this.height;
            // 幅 ＜ 規定幅 && 高さ ＜ 規定高さ
            if (org_w < new_w && org_h < new_h) {
              // 幅・高さは変更しない
              new_w = org_w;
              new_h = org_h;
            } else {
              // 幅 ＞ 規定幅 || 高さ ＞ 規定高さ
              if (org_w > org_h) {
                // 幅 ＞ 高さ
                var percent_w = new_w / org_w;
                // 幅を規定幅、高さを計算
                new_h = Math.ceil(org_h * percent_w);
              } else if (org_w < org_h) {
                // 幅 ＜高さ
                var percent_h = new_h / org_h;
                // 高さを規定幅、幅を計算
                new_w = Math.ceil(org_w * percent_h);
              }
            }

            // リサイズ画像
            $('span').html($('<canvas>').attr({
              'id': 'canvas',
              'width': new_w,
              'height': new_h
            }));
            var ctx = $('#canvas')[0].getContext('2d');
            var resize_img = new Image();
            resize_img.src = reader.result;
            ctx.drawImage(resize_img, 0, 0, new_w, new_h);
          };
        }
        reader.readAsDataURL(file);
      });
    });
    </script>
    <h2>更新履歴</h2>
    <p>2021/09/06 Ver 0.0.2 アップロード画像のサムネイル表示に対応</p>
</div>
  </body>
</html>

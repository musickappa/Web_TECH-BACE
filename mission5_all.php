<?php


	// DB接続設定
	#mission1
	#echo "mission1<br>";
	$dsn = 'データベース';
	$user = 'ユーザー名';
	$password = 'パスワード';
	$pdo = new PDO($dsn, $user, $password, array(PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING));
	#echo "確認";
	

	#mission2   テーブル作成
	#ここでテーブル名は「tbtest」として、そこに登録できる項目（カラム）は
    #id ・自動で登録されていうナンバリング。
    #name ・名前を入れる。文字列、半角英数で32文字。
    #comment ・コメントを入れる。文字列、長めの文章も入る。
	$sql = "CREATE TABLE IF NOT EXISTS tbtest"
	." ("
	. "id INT AUTO_INCREMENT PRIMARY KEY,"
	. "name char(32),"
	. "comment TEXT,"
    . "date char(32),"
    . "pass_new TEXT"
	.");";

	#mission3   データベースに現在、どのようなテーブルが作成されているかを確認します。
	$stmt = $pdo->query($sql);
	$sql ='SHOW TABLES';
	$result = $pdo -> query($sql);
	foreach ($result as $row){
		echo $row[0];
		echo ":remake";
		echo '<br>';
	}
	echo "<hr>";
?>


<?php
#関数一覧
#編集のフォームへの書き換え
function h($str) {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
  }
?>



<?php

    #変数一覧
    $input_name = $_POST["name"];
    $input_com = $_POST["comment"];
    $input_del = $_POST["del"];
    $input_edit = $_POST["edit"];
    $ini = $_POST["ini"];
    $input_pass_new = $_POST["pass_n"];
    $input_pass_d = $_POST["pass_d"];
    $input_pass_e = $_POST["pass_e"];
    $input_date = date("Y年m月d日 H時i分s秒");


    $file_edit = "file_edit.txt";
    $file_edit_num = "file_edit_num.txt";

    #編集の有無の確認
    if(file_exists($file_edit)){
        $lines = file($file_edit,FILE_IGNORE_NEW_LINES);
        $line = explode("<>",$lines[0]);
        $edit_num=$line[0];
        echo "edit_num:".$edit_num."<br>";
    }


    if($input_edit!=""){
        $fp = fopen($file_edit_num,"w");
        fwrite($fp,$input_edit);
        fclose($fp);
    }


    if(file_exists($file_edit_num)){
        $lines = file($file_edit_num,FILE_IGNORE_NEW_LINES);
        $line = explode("<>",$lines[0]);
        $edit_num_id=$line[0];
        echo "edit_num_id:".$edit_num_id."<br>";
    }

?>



<?php
    #echo "mission5<br>";
    #mission5
    #データベースにテーブルをつくりましたが、まだ何もデータが入っていません。
    #INSERT文 で、データ（レコード）を登録してみましょう。
    if($edit_num == 0){
        echo "新規フォーム<br>";
        if($input_name!=""&&$input_com!=""){
            $sql = $pdo -> prepare("INSERT INTO tbtest (name, comment, date, pass_new) VALUES (:name, :comment, :date, :pass_new)");
            #$sql = $pdo -> prepare("INSERT INTO tbtest (name, comment, date) VALUES (:name, :comment, :date)");
            $sql -> bindParam(':name', $name, PDO::PARAM_STR);
            $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
            $sql -> bindParam(':date', $date, PDO::PARAM_STR);
            $sql -> bindParam(':pass_new', $pass_new, PDO::PARAM_STR);
            $name = $input_name;         
            $comment = $input_com;   
            $date = $input_date;
            $pass_new = $input_pass_new;
            $sql -> execute();
            
            #file_editに書き込む
            $edit_num = 0;
            $f = fopen($file_edit,"w");
            fwrite($f,$edit_num);
            fclose($f);
        }
    }

    elseif($edit_num == 1){
        echo "編集フォーム<br>";
        $id = $edit_num_id;
        $name = $input_name;
        $comment = $input_com; 
        $sql = 'UPDATE tbtest SET name=:name,comment=:comment WHERE id=:id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);
        $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        #file_editに書き込む
        $edit_num = 0;
        $f = fopen($file_edit,"w");
        fwrite($f,$edit_num);
        fclose($f);
    }
       
?>



<?php
    #更新
    #データベースのテーブルに登録したデータレコードは、UPDATE文 で更新する事が可能です。
    $sql_show = 'SELECT * FROM tbtest';
    $stmt = $pdo->query($sql_show);
    $results = $stmt->fetchAll();

    if($input_edit!=""){
        echo "パスワード確認<br>";
        if($input_pass_e==$results[$input_edit-1]["pass_new"]){
            echo "編集<br>";
            $id = $input_edit; //変更する投稿番号
            ##############################
            $sql = 'SELECT * FROM tbtest';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            ##############################
            echo "name:".$results[$input_edit-1]["name"]."<br>";
            $form_name = h($results[$input_edit-1]["name"]);
            $form_com = h($results[$input_edit-1]["comment"]);
            
            #file_editに書き込む
            $edit_num = 1;
            $f = fopen($file_edit,"w");
            fwrite($f,$edit_num);
            fclose($f);
        }
    }
?>

<html>
    <head>
    <meta charset="UTF-8">
    <title>sample</title>
    
    </head>
    <body>
        <form action="" method="post">
        <input type="text" name="comment" placeholder="コメント" value="<?php echo $form_com?>">
        <input type="text" name="name" placeholder="名前" value="<?php echo $form_name?>">
        <input type="text" name="pass_n" placeholder="パスワードを決定">
        <input type="submit" name="submit">
        <br><br>
        <input type="number" name="del" placeholder="削除する番号">
        <input type="text" name="pass_d" placeholder="指定パスワードを入力">
        <input type="submit" name="submit">
        <br><br>
        <input type="number" name="ini" placeholder="初期化フォーム">
        <input type="submit" name="submit">
        <br><br>
        <input type="number" name="edit" placeholder="編集内容番号">
        <input type="text" name="pass_e" placeholder="指定パスワードを入力">
        <input type="submit" name="submit">
        <br><br>
        
        </form>
    </body>
</html>
<?php

    #初期化
    if($ini!=""){
        echo "初期化<br>";
        $sql = 'DROP TABLE tbtest';
        $stmt = $pdo->query($sql);
    }

    $sql_show = 'SELECT * FROM tbtest';
    $stmt = $pdo->query($sql_show);
    $results = $stmt->fetchAll();

    #削除する
    if($input_del!=""){
        if($input_pass_d==$results[$input_del-1]["pass_new"]){
            echo "削除<br>";
            $id = $input_del;
            $sql = 'delete from tbtest where id=:id';
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
        }
    }
    
    #表示する
    #echo "mission6<br>";
    echo "表示する<br>";
    #mission6
    #テーブルに登録されたデータを取得し、表示しましょう。
    $sql_show = 'SELECT * FROM tbtest';
    $stmt = $pdo->query($sql_show);
    $results = $stmt->fetchAll();
    foreach ($results as $row){
        //$rowの中にはテーブルのカラム名が入る
        #echo $results[0]["id"]."<br>";
        echo $row['id'].',';
        echo $row['name'].',';
        echo $row['comment'].',';
        echo $row['date'].',';
        echo $row['pass_new'].'<br>';
        echo "<hr>";
    }
    

?>
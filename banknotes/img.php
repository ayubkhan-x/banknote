<meta charset="UTF-8">

<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set("log_errors", 1);
ini_set("error_log", dirname(__FILE__)."/php-error.log");


$host = 'mysql8';  
$user = '39330634_banknotewiki';    
$pass = 'eWgl1bn8'; 
$db_name = '39330634_banknotewiki'; 
  $link = mysqli_connect($host, $user, $pass, $db_name); // Соединяемся с базой

  // Ругаемся, если соединение установить не удалось
  if (!$link) {
    echo 'Can not connect to DB. Error code: ' . mysqli_connect_errno() . ', error: ' . mysqli_connect_error();
    exit;
  }
?>

<?php

if (isset($_POST['AddVar'])) {
    if (!empty($_POST['pick_num'])) {

        $pick = $_POST['pick_num'];
        $banknote = $_POST['banknote'];
        $order = $_POST['order'];

        $sql = "INSERT INTO b_varieties (banknote_id, order_id, pick_num) VALUES ('" . $banknote . "', '" . $order . "', '" . $pick . "')";

        $insert = mysqli_query($link, $sql);

        if ($insert) {
            echo "Variety successfully added!";
        } else {
            echo "Error" . mysqli_error($link);
        }
    }
}




?>

<?php

  $country_id = NULL;
  $banknote_id = NULL;
  $img_front = NULL;
  $img_back = NULL;

  $sql = mysqli_query($link, 'SELECT * FROM `banknotes` WHERE `id` = '.$_GET['b']);
  while ($result = mysqli_fetch_array($sql)) {

    $country_id = $result['country_id'];
    $banknote_id = $result['id'];
    $img_front = $result['img_front'];
    $img_back = $result['img_back'];

    echo "<h1><a href='p.php?c={$result['country_id']}'>&larr;</a> {$result['ref1']} {$result['ref2']} {$result['ref3']} {$result['ref4']} {$result['ref5']} {$result['name']}</h1>";
  }
?>


<?php

echo "<img src='https://banknote.wiki/banknotes/{$country_id}/{$img_front}' width='100px'>";
echo "<img src='https://banknote.wiki/banknotes/{$country_id}/{$img_back}' width='100px'>";

echo "<form action='' method='post'>
      Front Link 360 <input type='text' name='img_f' id='ImgF'>
      Back Link  360 <input type='text' name='img_b' id='ImgB'>

      <input type='hidden' name='banknote' id='Banknote' value='{$banknote_id}'> 

      <input type='submit' value='Add Imgs' name='AddImg'>
      </form>";

?>

<hr>


<?php

$order_id = NULL;

$sql2 = mysqli_query($link, 'SELECT * FROM `b_varieties` WHERE `banknote_id` = '.$_GET['b'].' ORDER BY `order_id` ASC');
while ($result2 = mysqli_fetch_array($sql2)) {

  $var_id = $result2['id'];
  $order_id = $result2['order_id'];

  echo "<p>".$result2['pick_num']."</p>";
  echo "<p>".$var_id."</p>";
  echo "<img src='https://banknote.wiki/banknotes/{$country_id}/{$result2['img_front']}' width='200px'>";
  echo "<img src='https://banknote.wiki/banknotes/{$country_id}/{$result2['img_back']}' width='200px'>";


echo "<form action='' method='post'>
      Front Link 600 <input type='text' name='img_f' id='ImgF'>
      Back Link 600 <input type='text' name='img_b' id='ImgB'>

      <input type='hidden' name='banknote' id='Banknote' value='{$var_id}'> 

      <input type='submit' value='Add Imgs' name='AddImg'>
      </form>";

}

$order=$order_id+1;

echo "<form action='' method='post'>
      Var Pick <input type='text' name='pick_num' id='PickNum'>

      <input type='hidden' name='banknote' id='Banknote' value='{$banknote_id}'>
      <input type='hidden' name='order' id='Order' value='{$order}'>

      <input type='submit' value='Add Variety' name='AddVar'>
      </form>";

?>














<?php
/*
  $issue_id = NULL;


  $list1 = mysqli_query($link, 'SELECT * FROM `c_era` WHERE `country_id` = '.$_GET['c'].' ORDER BY `order_id` ASC');
  echo "<ul>";
  while ($result1 = mysqli_fetch_array($list1)) {
    echo "<li>{$result1['name']}</li>";

      $list2 = mysqli_query($link, 'SELECT * FROM `c_currency` WHERE `era_id` = '.$result1['id'].' ORDER BY `order_id` ASC');
      echo "<ul>";
      while ($result2 = mysqli_fetch_array($list2)) {
        echo "<li>{$result2['name']}</li>";

          $list3 = mysqli_query($link, 'SELECT * FROM `c_issue` WHERE `currency_id` = '.$result2['id'].' ORDER BY `order_id` ASC');
          echo "<ul>";
          while ($result3 = mysqli_fetch_array($list3)) {
            echo "<li>{$result3['name']}</li>";

              $order_id = NULL;
              $issue_id = $result3['id'];
              $currency_id = $result3['currency_id'];
              $era_id = $result3['era_id'];
              $country_id = $result3['country_id'];
              //$order_id = $result3['order_id'];

              $list4 = mysqli_query($link, 'SELECT * FROM `banknotes` WHERE `issue_id` = '.$result3['id'].' ORDER BY `order_id` ASC');
              echo "<ul><li>Pick# ";
              while ($result4 = mysqli_fetch_array($list4)) {
                echo "<a href='var.php?b={$result4['id']}'>{$result4['ref1']}</a>, ";

                $order_id = $result4['order_id'];
              }

              $o = $order_id+1;

              echo "<br><i>p+ c=".$country_id." e=".$era_id." c=".$currency_id." i=".$issue_id." o=".$o."</i>";

              echo "<form action='' method='post'>
                    <input type='text' name='ref1P' id='Ref1P'> 
                    <input type='hidden' name='orderP' id='OrderP' value='{$o}'> 
                    <input type='hidden' name='countryP' id='CountryP' value='{$country_id}'>
                    <input type='hidden' name='eraP' id='EraP' value='{$era_id}'>
                    <input type='hidden' name='currencyP' id='CurrencyP' value='{$currency_id}'>
                    <input type='hidden' name='issueP' id='IssueP' value='{$issue_id}'>
                    <input type='submit' value='Add Pick#' name='AddPick'>
                    </form>";

              echo "</li></ul>";

          }


          echo "</ul>";

      }


      echo "</ul>";

  }


  echo "</ul>";
*/
?>

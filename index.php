<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
</head>
<body>

<form name="form" action="" method="POST">
    <input type="number" class="subject" name="subject" id="subject" value="" placeholder="Введите число" min="1"
           max="1000000">
    <input id="button" type="submit" class="btn btn_export_csv_submit" value="Ввести">
</form>

<?php

$host = 'localhost';
$user = 'root';
$pass = '';
$name = 'test';

$link = mysqli_connect($host, $user, $pass, $name);

$arrPolis = array();

//$polis = $_POST['subject'];

$polis = '000006';

$polisExist = "SELECT POLIS FROM TEST_TABLE";

$arrExist = mysqli_query($link, $polisExist) or die(mysqli_error($link));

foreach ($arrExist as $row) {
   $arrPolis[] = $row['POLIS'];
}

if (in_array($polis, $arrPolis)) {
   echo $polis . " найден";
   $query = "SELECT POLIS, V_POS, SUM_POS, MES_N, YEAR(MES_N) AS YEAR, MONTH(MES_N) AS MONTH FROM TEST_TABLE WHERE POLIS ='" . $polis . "' ORDER BY V_POS, MES_N";

   $result = mysqli_query($link, $query) or die(mysqli_error($link));
   $i = 0;
   for ($data = []; $row = mysqli_fetch_assoc($result); $data[] = $row) ;

   $year_tmp = '';
   $year = array();
   foreach ($data as $el) {
      $year[] .= $el['YEAR'];
      $year = array_unique($year);
      asort($year);
   }
   ?>
    <table>
        <tr>
            <th rowspan="2">POLIS</th>
            <th rowspan="2">V_POS</th>
           <?php
           foreach ($year as $yearStr)
              echo '<th rowspan="1" colspan="12">' . $yearStr . '</th>';
           ?>
        </tr>
        <tr>
           <?
           $monthNum = '';
           $month = array();
           for ($monthNum = 1; $monthNum < 13; $monthNum++) {
              if ($monthNum > 0 && $monthNum < 10) {
                 $monthNum = '0' . $monthNum;
                 echo '<td>' . $monthNum . '</td>';
              } else {
                 echo '<td>' . $monthNum . '</td>';
              }
              $month[] .= $monthNum;
           }
           ?>
        </tr>
       <?php
       $revpos = 0;
       $revvpos = 0;
       $arr = array_fill_keys($month, "-");
       $len = count($data);
       foreach ($data as $elem): $i++; ?>
          <?php if ($revpos == 0) {
             $revpos = $elem['POLIS'];
             $revvpos = $elem['V_POS'];
             foreach ($month as $val) {
                if (date("m", strtotime($elem['MES_N'])) == $val) {
                   $arr[$val] = $elem['SUM_POS'];
                }
             }
          } else {
             if ($revpos == $elem['POLIS'] && $revvpos == $elem['V_POS']) {
                foreach ($month as $val) {
                   if (date("m", strtotime($elem['MES_N'])) == $val) {
                      $arr[$val] = $elem['SUM_POS'];
                   }
                }
             } else {
                ?>
                 <tr>
                     <td id="rs"><?= $revpos ?></td>
                     <td><?= $revvpos ?></td>
                    <?
                    foreach ($month as $val) {
                       echo '<td>' . $arr[$val] . '</td>';
                    }
                    ?>
                 </tr>
                <?php
                $revpos = $elem['POLIS'];
                $revvpos = $elem['V_POS'];
                $arr = array_fill_keys($month, "-");
                foreach ($month as $val) {
                   if (date("m", strtotime($elem['MES_N'])) == $val) {
                      $arr[$val] = $elem['SUM_POS'];
                   }
                }
             }
          }
          if ($i == $len) {
             ?>
              <tr>
                  <td id="disp"><?= $revpos ?></td>
                  <td><?= $revvpos ?></td>
                 <?
                 foreach ($month as $val) {
                    echo '<td>' . $arr[$val] . '</td>';
                 }
                 ?>
              </tr>
             <?php
          }
       endforeach; ?>
    </table>
<?php


} else {
   echo $polis . " не найден";
}
?>

</body>
<style>
    table {
        border-collapse: collapse;
        width: 25%;
    }

    table th, table td {
        padding: 10px;
        border: 1px solid black;
    }

    .display {
        display: none;
    }

    input[type="number"]::-webkit-outer-spin-button,
    input[type="number"]::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    input[type="number"] {
        -moz-appearance: textfield;
    }

    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
</style>
<script>
    let tr = document.querySelectorAll("tr");
    let rs = document.querySelector("#rs");
    let disp = document.querySelector("#disp");
    if (tr.length == 4) {
        rs.setAttribute('rowspan', '2');
        disp.setAttribute('class', 'display');
    }
    </html>

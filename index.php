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

//   $year_tmp = '';
//   $year = array();
//   foreach ($data as $el) {
//      $year[] .= $el['YEAR'];
//      $year = array_unique($year);
//      asort($year);
//   }

//   $year_tmp = '';
//   foreach ($data as $el) {
//      if ($year_tmp == '') {
//      } elseif ($year_tmp == $el['YEAR']) {
//         print("+");
//      } else {
//         print("-");
//      }
//      $year_tmp = $el['YEAR'];
//   }
$main_arr = array();
$v_pos_arr = array();
$year_arr = array();

foreach ($data as $v_pos) {
   $v_pos_arr[] = $v_pos['V_POS'];
}

foreach ($data as $year) {
   $year_arr[] = $year['YEAR'];
}
?>
<table>
    <tr>
        <th rowspan="2">POLIS</th>
        <th rowspan="2">V_POS</th>
       <?php
       $v_pos_arr = array_unique($v_pos_arr);
       $year_arr = array_unique($year_arr);
       asort($year_arr);
       foreach ($year_arr as $yearStr) {
          echo '<th rowspan="1" colspan="12">' . $yearStr . '</th>';
       } ?>
    </tr>
    <tr>
       <?php
       $monthNum = '';
       $month = array();
       foreach ($year_arr as $item) {
          for ($monthNum = 1; $monthNum < 13; $monthNum++) {
                echo '<td>' . $monthNum . '</td>';

             $month[] = $monthNum;
          }
       }
       $month = array_unique($month);
       ?>
    </tr>
   <?php
   $revpos = 0;
   $revvpos = 0;
   $arr = array_fill_keys($month, "-");
   $len = count($data);
   foreach ($data as $elem): $i++;
      $main_arr[$elem['V_POS']][$elem['YEAR']][$elem['MONTH']] = $elem['SUM_POS'];
      ?>
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
   endforeach; ?>

   <?php
   foreach ($v_pos_arr as $item) { ?>
       <tr>
           <td>
              <?= $polis ?>
           </td>
           <td>
              <?= $item ?>
           </td>
          <?php foreach ($year_arr as $year) {
             foreach ($month as $mt) {
                if (isset($main_arr[$item][$year][$mt])) {
                   echo '<td>' . $main_arr[$item][$year][$mt] . '</td>';
                } else {
                   echo '<td> - </td>';
                }
             }
          } ?>
       </tr>
      <?php
   }
   } else {
      echo $polis . " не найден";
   }
   ?>
</table>
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

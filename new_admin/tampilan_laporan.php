<?php require_once('../Connections/koneksi.php'); ?>
<?php
if (!function_exists("GetSQLValueString")) {
  function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "")
  {
    if (PHP_VERSION < 6) {
      $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
    }

    $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

    switch ($theType) {
      case "text":
        $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
        break;
      case "long":
      case "int":
        $theValue = ($theValue != "") ? intval($theValue) : "NULL";
        break;
      case "double":
        $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
        break;
      case "date":
        $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
        break;
      case "defined":
        $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
        break;
    }
    return $theValue;
  }
}

$maxRows_tampil_laporan = 500;
$pageNum_tampil_laporan = 0;
if (isset($_GET['pageNum_tampil_laporan'])) {
  $pageNum_tampil_laporan = $_GET['pageNum_tampil_laporan'];
}
$startRow_tampil_laporan = $pageNum_tampil_laporan * $maxRows_tampil_laporan;

$lap = $_GET['lap'];

mysql_select_db($database_koneksi, $koneksi);
$query_tampil_laporan = "SELECT * FROM laporan WHERE
                                              nisn LIKE '%$lap%' OR
                                              tahun LIKE '%$lap%' OR
                                              nominal LIKE '%$lap%' OR
                                              jumlah_bayar LIKE '%$lap%' OR
                                              sisa_bayar LIKE '%$lap%'";
$query_limit_tampil_laporan = sprintf("%s LIMIT %d, %d", $query_tampil_laporan, $startRow_tampil_laporan, $maxRows_tampil_laporan);
$tampil_laporan = mysql_query($query_limit_tampil_laporan, $koneksi) or die(mysql_error());
$row_tampil_laporan = mysql_fetch_assoc($tampil_laporan);

if (isset($_GET['totalRows_tampil_laporan'])) {
  $totalRows_tampil_laporan = $_GET['totalRows_tampil_laporan'];
} else {
  $all_tampil_laporan = mysql_query($query_tampil_laporan);
  $totalRows_tampil_laporan = mysql_num_rows($all_tampil_laporan);
}
$totalPages_tampil_laporan = ceil($totalRows_tampil_laporan / $maxRows_tampil_laporan) - 1;

$setengah = $row_tampil_laporan['nominal'] * 50 / 100;
?>

<div class="table-responsive">
  <table id="tabel" class="table table-bordered table-hover table-info">
    <tr class="font-weight-bold bg-info text-light">
      <td>Nisn</td>
      <td>Tahun</td>
      <td>Nominal</td>
      <td>Jumlah Bayar</td>
      <td>Sisa Bayar</td>
      <td>Delete</td>
      <td>View</td>
    </tr>
    <?php do { ?>
      <tr class="td">
        <td><?php echo $row_tampil_laporan['nisn']; ?></td>
        <td><?php echo $row_tampil_laporan['tahun']; ?></td>
        <td><?php echo $row_tampil_laporan['nominal']; ?></td>
        <td><?php echo $row_tampil_laporan['jumlah_bayar']; ?></td>
        <?php if ($row_tampil_laporan['jumlah_bayar'] == $row_tampil_laporan['nominal']) { ?>
        <td class="font-weight-bold text-success"><?php echo $row_tampil_laporan['sisa_bayar']; ?></td>
        <?php } elseif ($row_tampil_laporan['jumlah_bayar'] != $row_tampil_laporan['nominal'] && $row_tampil_laporan['sisa_bayar'] > $setengah) { ?>
        <td class="font-weight-bold text-danger"><?php echo $row_tampil_laporan['sisa_bayar']; ?></td>
        <?php }elseif ($row_tampil_laporan['jumlah_bayar'] != $row_tampil_laporan['nominal'] && $row_tampil_laporan['sisa_bayar'] <= $setengah) {  ?>
        <td class="font-weight-bold text-primary"><?php echo $row_tampil_laporan['sisa_bayar']; ?></td>
        <?php } ?>
        <td><a class="btn btn-sm btn-danger col-sm-4" onclick="return confirm('Apa kamu yakin ingin menghapus nisn <?php echo $row_tampil_laporan['nisn']; ?>');" href="delete_laporan.php?nisn=<?php echo $row_tampil_laporan['nisn']; ?>"><i class="fas fa-edit"></i></a></td>
        <td>
          <a class="btn btn-sm btn-warning col-sm-4" href="tampil_lihat.php?nisn=<?php echo $row_tampil_laporan['nisn']; ?>"><i class="fas fa-file-excel"></i></a>

          <a class="btn btn-sm btn-warning col-sm-4" href="tampil_export.php?nisn=<?php echo $row_tampil_laporan['nisn']; ?>"><i class="fas fa-file-word"></i></a>
        </td>
      </tr>
    <?php } while ($row_tampil_laporan = mysql_fetch_assoc($tampil_laporan)); ?>
  </table>
</div>
<?php
mysql_free_result($tampil_laporan);
?>
<?php
/////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////
//// SISFO-TOKO v2.1                                 ////
/////////////////////////////////////////////////////////
//// Dibuat Oleh :                                   ////
////    Agus Muhajir, S.Kom                          ////
/////////////////////////////////////////////////////////
//// URL    : http://hajirodeon.wordpress.com/       ////
//// E-Mail : hajirodeon@yahoo.com                   ////
//// HP/SMS : 081-829-88-54                          ////
/////////////////////////////////////////////////////////
//// Milist :                                        ////
////    http://yahoogroup.com/groups/linuxbiasawae/  ////
////    http://yahoogroup.com/groups/sisfokol/       ////
/////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////


session_start();

require("../../inc/config.php");
require("../../inc/fungsi.php");
require("../../inc/koneksi.php");
require("../../inc/cek/adm.php");
require("../../inc/class/paging.php");
$tpl = LoadTpl("../../template/index.html");

nocache;

//nilai
$filenya = "hutang_beli_sup.php";
$judul = "Hutang Pembelian [Per Supplier]";
$judulku = "[$admin_session : $username1_session] ==> $judul";
$judulx = $judul;
$supkd = nosql($_REQUEST['supkd']);
$page = nosql($_REQUEST['page']);
if ((empty($page)) OR ($page == "0"))
	{
	$page = "1";
	}


//focus
//nek sih null
if (empty($supkd))
	{
	$diload = "document.formx.supplier.focus();";
	}



//PROSES ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//jika simpan
if ($_POST['btnSMP'])
	{
	//nilai
	$page = nosql($_POST['page']);
	$supkd = nosql($_POST['supkd']);
	$ke = "$filenya?supkd=$supkd";

	//query
	$p = new Pager();
	$start = $p->findStart($limit);

	$sqlcount = "SELECT * FROM beli ".
					"WHERE round(DATE_FORMAT(tgl_lunas, '%d')) = '00' ".
					"AND round(DATE_FORMAT(tgl_lunas, '%m')) = '00' ".
					"AND round(DATE_FORMAT(tgl_lunas, '%Y')) = '0000' ".
					"AND kd_supplier = '$supkd' ".
					"ORDER BY tgl_beli DESC";

	$sqlresult = $sqlcount;

	$count = mysql_num_rows(mysql_query($sqlcount));
	$pages = $p->findPages($count, $limit);
	$result = mysql_query("$sqlresult LIMIT ".$start.", ".$limit);
	$pagelist = $p->pageList($_GET['page'], $pages, $target);
	$data = mysql_fetch_array($result);


	do
		{
		//ambil nilai
		$ongko = $ongko + 1;

		$xkd = "kd";
		$xkd1 = "$xkd$ongko";
		$xkdx = nosql($_POST["$xkd1"]);

		$xbyr = "byr";
		$xbyr1 = "$xbyr$ongko";
		$xbyrx = nosql($_POST["$xbyr1"]);

		$xbank = "bank";
		$xbank1 = "$xbank$ongko";
		$xbankx = nosql($_POST["$xbank1"]);

		$xltgl = "ltgl";
		$xltgl1 = "$xltgl$ongko";
		$xltglx = nosql($_POST["$xltgl1"]);

		$xlbln = "lbln";
		$xlbln1 = "$xlbln$ongko";
		$xlblnx = nosql($_POST["$xlbln1"]);

		$xlthn = "lthn";
		$xlthn1 = "$xlthn$ongko";
		$xlthnx = nosql($_POST["$xlthn1"]);

		$tgl_l = "$xlthnx:$xlblnx:$xltglx";

		//query
		mysql_query("UPDATE beli SET kd_jns_byr = '$xbyrx', ".
						"bank = '$xbankx', ".
						"tgl_lunas = '$tgl_l' ".
						"WHERE kd = '$xkdx'");
		}
	while ($data = mysql_fetch_assoc($result));

	//null-kan
	xfree($qbw);
	xfree($result);
	xclose($koneksi);

	//re-direct
	xloc($ke);
	exit();
	}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////





//isi *START
ob_start();




//require
require("../../inc/js/jumpmenu.js");
require("../../inc/js/swap.js");
require("../../inc/js/listmenu.js");
require("../../inc/menu/adm.php");
require("../../inc/menu/adm_cek.php");


//view //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
echo '<form method="post" action="'.$filenya.'" name="formx">
<table width="100%" border="0" cellspacing="0" cellpadding="3">
<tr>
<td>';
xheadline($judul);
echo '</td>
</tr>
</table>

<table bgcolor="'.$warna02.'" width="100%" border="0" cellspacing="0" cellpadding="3">
<tr>
<td>
<strong>Supplier : </strong>';

//terpilih
$qsupx = mysql_query("SELECT * FROM m_supplier ".
						"WHERE kd = '$supkd'");
$rsupx = mysql_fetch_assoc($qsupx);
$supx_nm = balikin($rsupx['singkatan']);


echo "<select name=\"supplier\" onChange=\"MM_jumpMenu('self',this,0)\">";
echo '<option value="'.$supkd.'" selected>'.$supx_nm.'</option>';

//query
$qsup = mysql_query("SELECT * FROM m_supplier ".
						"WHERE kd <> '$supkd' ".
						"ORDER BY singkatan ASC");
$rsup = mysql_fetch_assoc($qsup);

do
	{
	$sup_kd = nosql($rsup['kd']);
	$sup_nm = balikin($rsup['singkatan']);
	echo '<option value="'.$filenya.'?supkd='.$sup_kd.'">'.$sup_nm.'</option>';
	}
while ($rsup = mysql_fetch_assoc($qsup));

echo '</select>
</td>
</tr>
</table>
<br>';


//cek
if (empty($supkd))
	{
	echo '<strong>Supplier Belum Dipilih...!</strong>';
	}
else
	{
	//query
	$p = new Pager();
	$start = $p->findStart($limit);

	$sqlcount = "SELECT beli.*, DATE_FORMAT(beli.tgl_lunas, '%d') AS ltgl, ".
					"DATE_FORMAT(beli.tgl_lunas, '%m') AS lbln,  ".
					"DATE_FORMAT(beli.tgl_lunas, '%Y') AS lthn ".
					"FROM beli ".
					"WHERE round(DATE_FORMAT(tgl_lunas, '%d')) = '00' ".
					"AND round(DATE_FORMAT(tgl_lunas, '%m')) = '00' ".
					"AND round(DATE_FORMAT(tgl_lunas, '%Y')) = '0000' ".
					"AND kd_supplier = '$supkd' ".
					"ORDER BY tgl_beli DESC";
	$sqlresult = $sqlcount;

	$count = mysql_num_rows(mysql_query($sqlcount));
	$pages = $p->findPages($count, $limit);
	$result = mysql_query("$sqlresult LIMIT ".$start.", ".$limit);
	$target = "$filenya?supkd=$supkd";
	$pagelist = $p->pageList($_GET['page'], $pages, $target);
	$data = mysql_fetch_array($result);

	if ($count != 0)
		{
		echo '<table width="900" border="1" cellspacing="0" cellpadding="3">
		<tr bgcolor="'.$warnaheader.'">
		<td width="80" align="center"><strong><font color="'.$warnatext.'">Tanggal</font></strong></td>
		<td align="center"><strong><font color="'.$warnatext.'">No. Faktur</font></strong></td>
		<td width="80" align="center"><strong><font color="'.$warnatext.'">Jenis Pembayaran</font></strong></td>
		<td width="150" align="center"><strong><font color="'.$warnatext.'">Total</font></strong></td>
		<td width="100" align="center"><strong><font color="'.$warnatext.'">Bank</font></strong></td>
		<td width="220" align="center"><strong><font color="'.$warnatext.'">Tgl. Pelunasan</font></strong></td>
		</tr>';

		do
			{
			if ($warna_set ==0)
				{
				$warna = $warna01;
				$warna_set = 1;
				}
			else
				{
				$warna = $warna02;
				$warna_set = 0;
				}

			$nomer = $nomer + 1;
			$y_kd = nosql($data['kd']);
			$y_tgl_beli = $data['tgl_beli'];
			$y_no_faktur = balikin($data['no_faktur']);
			$y_kd_byr = nosql($data['kd_jns_byr']);
			$y_bank = balikin($data['bank']);
			$y_diskon = nosql($data['diskon']);
			$y_tot_byr = nosql($data['total_bayar']);

			//total sementara
			$qduwi = mysql_query("SELECT SUM(subtotal) AS subtotal ".
									"FROM beli_detail ".
									"WHERE kd_beli = '$y_kd' ".
									"AND bonus = 'false'");
			$rduwi = mysql_fetch_assoc($qduwi);
			$y_total_beli = nosql($rduwi['subtotal']);
			$y_total_diskon = round((($y_diskon * $total_beli)/100),2);


			//total
			//nek gak ada diskon
			if (empty($y_diskon))
				{
				$y_total_bayar = $y_total_beli;
				}
			else if (empty($t_tot_byr))
				{
				$y_total_bayar = $y_tot_byr;
				}
			else
				{
				$y_total_bayar = round($y_total_beli - $y_total_diskon,2);
				}

			//nek null
			if (empty($y_total_bayar))
				{
				$y_total_bayarx = "-";
				}
			else
				{
				$y_total_bayarx = $y_total_bayar;
				}



			//tgl. pelunasan
			$y_ltgl = nosql($data['ltgl']);
			$y_lbln = nosql($data['lbln']);
			$y_lthn = nosql($data['lthn']);

			if ($y_ltgl == "00")
				{
				$y_ltgl = "";
				}

			if ($y_lthn == "000")
				{
				$y_lthn = "";
				}




			echo "<tr valign=\"top\" bgcolor=\"$warna\" onmouseover=\"this.bgColor='$warnaover';\" onmouseout=\"this.bgColor='$warna';\">";
			echo '<td>
			<input name="kd'.$nomer.'" type="hidden" value="'.$y_kd.'">
			'.$y_tgl_beli.'
			</td>
			<td>'.$y_no_faktur.'</td>

			<td align="center">
			<select name="byr'.$nomer.'">';

			//terpilih
			$qbyrx = mysql_query("SELECT * FROM m_jns_byr ".
									"WHERE kd = '$y_kd_byr'");
			$rbyrx = mysql_fetch_assoc($qbyrx);
			$byrx_nm = balikin($rbyrx['jns_byr']);

			echo '<option value="'.$y_kd_byr.'" selected>'.$byrx_nm.'</option>';

			//data jenis pembayaran
			$qbyr = mysql_query("SELECT * FROM m_jns_byr ".
									"WHERE kd <> '$y_kd_byr'");
			$rbyr = mysql_fetch_assoc($qbyr);

			do
				{
				$byr_kd = nosql($rbyr['kd']);
				$byr_nm = balikin($rbyr['jns_byr']);

				echo '<option value="'.$byr_kd.'">'.$byr_nm.'</option>';
				}
			while ($rbyr = mysql_fetch_assoc($qbyr));

			echo '</select>
			</td>
			<td align="right">
			'.$y_total_bayarx.'
			</td>

			<td>
			<input name="bank'.$nomer.'" type="text" value="'.$y_bank.'" size="15">
			</td>


			<td>

			<select name="ltgl'.$nomer.'">
			<option value="'.$y_ltgl.'" selected>'.$y_ltgl.'</option>';
			for ($i=1;$i<=31;$i++)
				{
				echo '<option value="'.$i.'">'.$i.'</option>';
				}

			echo '</select>
			<select name="lbln'.$nomer.'">
			<option value="'.$y_lbln.'" selected>'.$arrbln1[$y_lbln].'</option>';
			for ($j=1;$j<=12;$j++)
				{
				echo '<option value="'.$j.'">'.$arrbln[$j].'</option>';
				}

			echo '</select>
			<select name="lthn'.$nomer.'">
			<option value="'.$y_lthn.'" selected>'.$y_lthn.'</option>';

			//query
			$qthn2 = mysql_query("SELECT * FROM m_tahun ".
									"ORDER BY tahun DESC");
			$rthn2 = mysql_fetch_assoc($qthn2);

			do
				{
				$x_thn2 = nosql($rthn2['tahun']);
				echo '<option value="'.$x_thn2.'">'.$x_thn2.'</option>';
				}
			while ($rthn2 = mysql_fetch_assoc($qthn2));

			echo '</select>
			</td>

	        </tr>';
			}
		while ($data = mysql_fetch_assoc($result));

		echo '</table>
		<table width="900" border="0" cellspacing="0" cellpadding="3">
		<tr>
		<td>
		<input name="supkd" type="hidden" value="'.$supkd.'">
		<input name="page" type="hidden" value="'.$page.'">
		<input name="btnSMP" type="submit" value="SIMPAN">
		</td>

		<td align="right"><strong><font color="#FF0000">'.$count.'</font></strong> Data. '.$pagelist.'
		</td>
		</tr>
		</table>';
		}
	else
		{
		echo '<font color="red"><strong>TIDAK ADA DAFTAR HUTANG.</strong></font>';
		}
	}

echo '</form>
<br><br><br>';

//isi
$isi = ob_get_contents();
ob_end_clean();

require("../../inc/niltpl.php");


//null-kan
xfree($qbw);
xfree($result);
xclose($koneksi);
exit();
?>
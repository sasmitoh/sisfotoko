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
require("../../inc/class/paging2.php");
require("../../inc/cek/adm.php");
$tpl = LoadTpl("../../template/index.html");

nocache;

//nilai
$filenya = "tahun.php";
$diload = "document.formx.tahun.focus();";
$judul = "Data Tahun Periode";
$judulku = "[$admin_session : $username1_session] ==> $judul";
$judulx = $judul;
$s = nosql($_REQUEST['s']);
$page = nosql($_REQUEST['page']);
if ((empty($page)) OR ($page == "0"))
	{
	$page = "1";
	}


//nek enter
$x_enter = 'onKeyDown="var keyCode = event.keyCode;
if (keyCode == 13)
	{
	document.formx.btnSMP.focus();
	}"';



//PROSES ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//nek batal
if ($_POST['btnBTL'])
	{
	//re-direct
	xloc($filenya);
	exit();
	}



//jika edit
if ($s == "edit")
	{
	$kdx = nosql($_REQUEST['kd']);

	$qx = mysql_query("SELECT * FROM m_tahun ".
						"WHERE kd = '$kdx'");
	$rowx = mysql_fetch_assoc($qx);

	$tahunx = nosql($rowx['tahun']);
	}



//jika simpan
if ($_POST['btnSMP'])
	{
	$s = nosql($_POST['s']);
	$kd = nosql($_POST['kd']);
	$tahun = cegah($_POST['tahun']);

	//nek null
	if (empty($tahun))
		{
		//null-kan
		xfree($qbw);
		xclose($koneksi);

		//re-direct
		$pesan = "Tahun Periode Belum Ditulis. Harap Diulangi...!!";
		pekem($pesan,$filenya);
		exit();
		}
	else
		{ ///cek
		$qcc = mysql_query("SELECT * FROM m_tahun ".
								"WHERE tahun = '$tahun'");
		$rcc = mysql_fetch_assoc($qcc);
		$tcc = mysql_num_rows($qcc);


		//nek duplikasi, lebih dari 1
		if ($tcc > 1)
			{
			//null-kan
			xfree($qbw);
			xfree($qcc);
			xclose($koneksi);

			//re-direct
			$pesan = "Ditemukan Duplikasi Tahun Periode : $tahun. Harap Segera Diperhatikan...!!";
			pekem($pesan,$filenya);
			exit();
			}
		else
			{
			//jika update
			if ($s == "edit")
				{
				mysql_query("UPDATE m_tahun SET tahun = '$tahun' ".
								"WHERE kd = '$kd'");

				//null-kan
				xfree($qbw);
				xfree($qcc);
				xclose($koneksi);

				//re-direct
				xloc($filenya);
				exit();
				}

			//jika baru
			if (empty($s))
				{
				//nek ada
				if ($tcc != 0)
					{
					//null-kan
					xfree($qbw);
					xfree($qcc);
					xclose($koneksi);

					//re-direct
					$pesan = "Tahun : $tahun, Sudah Ada. Silahkan Ganti Yang Lain...!!";
					pekem($pesan,$filenya);
					exit();
					}
				else
					{
					mysql_query("INSERT INTO m_tahun(kd, tahun) VALUES ".
									"('$x', '$tahun')");

					//null-kan
					xfree($qbw);
					xfree($qcc);
					xclose($koneksi);

					//re-direct
					xloc($filenya);
					exit();
					}
				}
			}
		}
	}


//jika hapus
if ($_POST['btnHPS'])
	{
	//ambil nilai
	$jml = nosql($_POST['jml']);

	//ambil semua
	for ($i=1; $i<=$jml;$i++)
		{
		//ambil nilai
		$yuk = "item";
		$yuhu = "$yuk$i";
		$kd = nosql($_POST["$yuhu"]);

		//del
		mysql_query("DELETE FROM m_tahun ".
						"WHERE kd = '$kd'");
		}

	//null-kan
	xfree($qbw);
	xclose($koneksi);

	//auto-kembali
	xloc($filenya);
	exit();
	}
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



//isi *START
ob_start();

//query
$p = new Pager();
$start = $p->findStart($limit);

$sqlcount = "SELECT * FROM m_tahun ".
				"ORDER BY tahun DESC";
$sqlresult = $sqlcount;

$count = mysql_num_rows(mysql_query($sqlcount));
$pages = $p->findPages($count, $limit);
$result = mysql_query("$sqlresult LIMIT ".$start.", ".$limit);
$pagelist = $p->pageList($_GET['page'], $pages, $target);
$data = mysql_fetch_array($result);


//require
require("../../inc/js/jumpmenu.js");
require("../../inc/js/checkall.js");
require("../../inc/js/number.js");
require("../../inc/js/swap.js");
require("../../inc/js/listmenu.js");
require("../../inc/menu/adm.php");
require("../../inc/menu/adm_cek.php");
xheadline($judul);

//view //////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
echo '<form action="'.$filenya.'" method="post" name="formx">
<p>
<input name="tahun" type="text" size="4" maxlength="4" value="'.$tahunx.'" onKeyPress="return numbersonly(this, event)" '.$x_enter.'>
<input name="btnSMP" type="submit" value="SIMPAN">
<input name="btnBTL" type="submit" value="BATAL">
</p>
<table width="450" border="1" cellspacing="0" cellpadding="3">
<tr valign="top" bgcolor="'.$warnaheader.'">
<td width="1%">&nbsp;</td>
<td width="1%">&nbsp;</td>
<td><strong><font color="'.$warnatext.'">Tahun Periode</font></strong></td>
</tr>';

if ($count != 0)
	{
	do {
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
		$kd = nosql($data['kd']);
		$tahun = nosql($data['tahun']);

		echo "<tr valign=\"top\" bgcolor=\"$warna\" onmouseover=\"this.bgColor='$warnaover';\" onmouseout=\"this.bgColor='$warna';\">";
		echo '<td>
		<input type="checkbox" name="item'.$nomer.'" value="'.$kd.'">
        </td>
		<td>
		<a href="'.$filenya.'?s=edit&kd='.$kd.'"><img src="'.$sumber.'/img/edit.gif" width="16" height="16" border="0"></a>
		</td>
		<td>'.$tahun.'</td>
        </tr>';
		}
	while ($data = mysql_fetch_assoc($result));
	}


echo '</table>
<table width="450" border="0" cellspacing="0" cellpadding="3">
<tr>
<td width="250">
<input name="jml" type="hidden" value="'.$count.'">
<input name="s" type="hidden" value="'.$s.'">
<input name="kd" type="hidden" value="'.$kdx.'">
<input name="btnALL" type="button" value="SEMUA" onClick="checkAll('.$count.')">
<input name="btnBTL" type="reset" value="BATAL">
<input name="btnHPS" type="submit" value="HAPUS">
</td>
<td align="right">
<strong><font color="#FF0000">'.$count.'</font></strong> Data. '.$pagelist.'
</td>
</tr>
</table>
</form>
<br><br><br>';

//isi
$isi = ob_get_contents();
ob_end_clean();

require("../../inc/niltpl.php");

//null-kan
xfree($qbw);
xclose($koneksi);
exit();
?>
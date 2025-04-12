<link rel="stylesheet" href="<?= base_url("assets/pdf/style.css"); ?>">
<style type="text/css">
    @media print {
        .pagebreak {
            page-break-after: always;
        }
    }

    table tr td {
        font-weight: bold;
        color: #000;
    }

    .footer-text {
        color: #000;
        font-size: 11px;
        font-weight: bold;
        font-family: Arial;
    }
</style>
<?php
error_reporting(E_ALL & ~E_NOTICE);
$urutan = 0;
$no_bum = explode(",", $nomor_bum);
foreach ($no_bum as $key => $nobum) {
    $detail = $this->m_list->getSQL("
        SELECT
            *
        FROM
            jurnal
        WHERE
        (tipe = 'BUM' OR tipe LIKE'J%')
            AND nomor = '" . $nobum . "'
            AND SUBSTR(no_perkiraan,1,4) NOT IN ('1101')
        ORDER BY
            debet DESC
    ");
    $jarh_detail  = $detail->result_array();
    $total_detail = $detail->num_rows();
    $jarh         = $this->m_list->getSQL("SELECT * FROM jarh WHERE nomor = '" . $nobum . "' ")->row();
    $cbg          = $this->m_list->getSQL("SELECT cabang FROM pastibisa_tb_cabang WHERE nocab='" . $jarh->kdcab . "'")->row();

    $jur_kasbank  = $this->m_list->getSQL("SELECT * FROM jurnal WHERE nomor = '" . $nobum . "' and (tipe = 'BUM' OR tipe LIKE'J%') and no_perkiraan like '1101%' order by nomor");
    $ar_jur_kasbank  = $jur_kasbank->result_array();
    $jml_jur_kasbank = $jur_kasbank->num_rows();

    $singkat_cbg    = $this->session->userdata('singkat_cbg');
    $cek_periode    = $this->db->query("SELECT * FROM periode WHERE stsaktif = 'O' and kdcab='$singkat_cbg'")->result();
    if ($cek_periode > 0) {
        foreach ($cek_periode as $brs_periode) {
            $tanggal_periode    = $brs_periode->periode;
            $bln                = substr($tanggal_periode, 0, 2);
            $thn                = substr($tanggal_periode, 3, 4);
        }
    }

    $kode_cabang    = $this->session->userdata('kode_cabang');
    $nokir_kasbank1  = $ar_jur_kasbank[$urutan]['no_perkiraan'];
    $data_buk_coa1    = $this->m_list->getSQL("SELECT * FROM COA WHERE no_perkiraan = '$nokir_kasbank1' and bln='$bln' and thn='$thn'")->row();

    $i            = 0;
    $j            = 1;
    $tdebet       = 0;
    $page         = ceil($total_detail / 10);
	$jumlahtotal=0;

    for ($j; $j <= $page; $j++) {
        if ($j > 1) {
            echo "<br />";
        }

        $limit = $j * 10;
?>
        <div class="header-no" style="font-family:Arial;font-size:14px;padding-bottom:-19px;">No. : <?php echo $jarh->nomor; ?></div>
        <div class="header-text" style="font-family:arial;font-size:14px;">BUKTI UANG MASUK <span></span></div>

        <table class="" width="100%" style="padding:0px;font-size:11px;font-family: verdana;border-spacing:0 -4px;">
            <!-- <tr>
                <td width="30%" style="border:none;text-align:center;"></td>
                <td width="16%" style="border:none;text-align:left;">
                    CASH/CHEQUE/BG NO.
                </td>
                <td width="4%" style="border:none;text-align:center;">:</td>
                <td width="45%" style="border:none;text-align:left;">
                    <?php echo $jarh->no_reff; ?>
                </td>
            </tr> -->
            <tr>
                <td width="30%" style="border:none;text-align:center;"></td>
                <td width="16%" style="border:none;text-align:left;">
                    Bank / Kas
                </td>
                <td width="4%" style="border:none;text-align:center;">:</td>
                <td width="45%" style="border:none;text-align:left;">
                    <!-- <?php echo $jarh->norek_bank; ?> -->
                    <?php echo $data_buk_coa1->nama; ?> (<?php echo $nokir_kasbank1; ?>)
                </td>
            </tr>
            <tr>
                <td width="30%" style="border:none;text-align:center;"></td>
                <td width="16%" style="border:none;text-align:left;">
                    Diterima dari
                </td>
                <td width="4%" style="border:none;text-align:center;">:</td>
                <td width="45%" style="border:none;text-align:left;">
                    <?php echo $jarh->terima_dari; ?>
                </td>
            </tr>
        </table>
        <div class="header-cab" style="font-family:Arial;padding-top:-2px;"><?php echo $cbg->cabang; ?></div>
        <table class="gridtable" width="100%" style="border-spacing:0 -3px;">
            <tr>
                <td align="center" style="border-top-style:dotted; border-right:none; border-bottom-style:dotted; border-left-style:dotted;width:5%;font-size:11px;">No</td>
                <td align="center" style="border-top-style:dotted; border-right:none; border-bottom-style:dotted; border-left-style:dotted;font-size:11px;">Keterangan</td>
                <td align="center" style="border-top-style:dotted; border-right:none; border-bottom-style:dotted; border-left-style:dotted;width:15%;font-size:11px;">Reff</td>
                <td align="left" style="border-top-style:dotted; border-right:none; border-bottom-style:dotted; border-left-style:dotted;width:13%;font-size:11px;">Nama COA</td>
                <td align="left" style="border-top-style:dotted; border-right:none; border-bottom-style:dotted; border-left-style:dotted;width:13%;font-size:11px;">No. COA</td>
                <td align="center" style="border-top-style:dotted; border-right:none; border-bottom-style:dotted; border-left-style:dotted;width:5%;font-size:11px;">D/K</td>
                <td align="right" style="border-top-style:dotted; border-right-style:dotted; border-bottom-style:dotted; border-left-style:dotted;width:12%;font-size:11px;">Jumlah (Rp.)</td>
            </tr>
            <?php
            $jdebet  = 1;
            $jkredit = 1;
            $debet   = 0;
            $kredit  = 0;

            for ($i; $i < $limit; $i++) {
            ?>
                <?php
                $jumlah        = '';
                $jenis_tr    = '';
                if (substr($jarh_detail[$i]["no_perkiraan"], 0, 4) !== "1101") {
					$jumlahtotal=($jumlahtotal+$jarh_detail[$i]["kredit"]-$jarh_detail[$i]["debet"]);
                    if ($i < $total_detail) {
                        if ($jarh_detail[$i]["debet"] > 0) {
                            $jenis_tr        = 'D';
                            $jumlah         = number_format($jarh_detail[$i]["debet"]);
                            $debet          = $debet + $jarh_detail[$i]["debet"];
                            $tdebet         = $tdebet + $jarh_detail[$i]["debet"];
                            $jdebet++;
                        } else if ($jarh_detail[$i]["kredit"] > 0) {
                            $jenis_tr        = 'K';
                            $jumlah         = number_format($jarh_detail[$i]["kredit"]);
                            $kredit         = $kredit + $jarh_detail[$i]["kredit"];
                            $jkredit++;
                        } else if ($jarh_detail[$i]["tp"] === "") {
                            $jumlah         = "";
                            $jenis_tr        = '';
                            $debet          = $debet + $jarh_detail[$i][""];
                            $tdebet         = $tdebet + $jarh_detail[$i][""];
                            $jdebet++;
                        }
                    }

                    $kode_cabang    = $this->session->userdata('kode_cabang');
                    $nokir_kasbank2[$i]  = $jarh_detail[$i]["no_perkiraan"];

                    $data_buk_coa2    = $this->db->query("SELECT * FROM COA WHERE no_perkiraan = '$nokir_kasbank2[$i]'")->result();
                    if ($nokir_kasbank2[$i] > 0) {
                        if ($data_buk_coa2 > 0) {
                            foreach ($data_buk_coa2 as $brs_coa2) {
                                $nama_coa2 = $brs_coa2->nama;
                            }
                        } else {
                            $nama_coa2 = "";
                        }
                    } else {
                        $nama_coa2 = "";
                    }
                ?>
                    <tr>
                        <td valign="top" class="text-center" style="border-right-style: dotted;border-left-style: dotted; border-bottom:none;border-top:none;font-size:11px;"><?php echo $i + 1; ?></td>
                        <td valign="top" class="text-left" style="border-left:none;border-right:none;border-bottom:none;border-top:none; font-size:11px;"><?php echo substr($jarh_detail[$i]["keterangan"], 0, 255); ?></td>
                        <td valign="top" class="text-left" style="border-left-style: dotted; border-right:none;border-bottom:none;border-top:none;font-size:10px;"><?php echo $jarh_detail[$i]["no_reff"]; ?></td>
                        <td valign="top" class="text-left" style="border-left-style: dotted; border-right:none;border-bottom:none;border-top:none;font-size:10px;"><?php echo $nama_coa2; ?></td>
                        <td valign="top" style="border-left-style: dotted; border-right:none;border-bottom:none;border-top:none;font-size:11px;"><?php echo $jarh_detail[$i]["no_perkiraan"]; ?></td>
                        <td valign="top" class="text-center" style="border-left-style: dotted; border-right:none;border-bottom:none;border-top:none;font-size:11px;"><?php echo $jenis_tr; ?></td>
                        <td valign="top" class="text-right" style="border-left-style: dotted; border-right-style: dotted; border-bottom:none;border-top:none;font-size:11px;">
                            <?php
                            echo $jumlah;
                            ?>
                        </td>
                    </tr>
            <?php
                }
            }
            ?>
            <?php if ((int) $page !== 1) { ?>
                <tr>
                    <td class="footer-detail-left" colspan="7" style="border-left-style: none; border-right:none; border-bottom-style: none;border-top-style: dotted;font-size:11px;"> </td>
                    <!-- <td class="footer-detail-left" colspan="3" style="border-left-style: dotted; border-right:none; border-bottom-style: dotted;border-top-style: dotted;font-size:11px;">SUB TOTAL</td>
                    <td class="text-right" style="border-left-style:dotted; border-right-style:dotted; border-bottom-style:dotted; border-top-style:dotted;font-size:11px;">
                        <?php
                        if ((int) $jumlahtotal !== 0) {
                            echo number_format($jumlahtotal);
                        } else {
                            echo number_format($jumlahtotal);
                        }
                        ?>
                    </td> -->
                </tr>
            <?php } ?>

            <?php if ((int) $j === (int) $page) { ?>
                <tr>
                    <?php
                    if ((int) $page !== 1) {
                    ?>
                        <td class="footer-detail-left" colspan="3" style="border-left-style: none; border-right:none; border-bottom-style: none;border-top-style: none;"></td>
                    <?php } else { ?>

                        <td class="footer-detail-left" colspan="3" style="border-left-style: none; border-right:none; border-bottom-style: none;border-top-style: dotted;font-size:11px;"></td>

                    <?php } ?>

                    <td class="footer-detail-left" colspan="3" style="border-left-style: dotted; border-right:none; border-bottom-style: dotted;border-top-style: dotted;font-size:11px;">TOTAL</td>
                    <td class="text-right" style="border-left-style:dotted; border-right-style:dotted; border-bottom-style:dotted; border-top-style:dotted;font-size:11px;">
                        <?php
                        echo number_format($jumlahtotal);
                        ?>
                    </td>
                </tr>
            <?php } ?>
        </table>

<?php if ((int) $j === (int) $page) { ?>
        <div class="footer-text" style="font-size:11px;padding-top:-20px;padding-bottom:-2px;">Terbilang :
            <?php
            if ((int) $j === (int) $page) {
                echo wordwrap(Terbilang($jumlahtotal)." Rupiah",65,"<br>");//substr(Terbilang($jumlahtotal), 0, 80);
            } else {
                echo wordwrap(Terbilang($jumlahtotal)." Rupiah",65,"<br>");// echo substr(Terbilang($jumlahtotal), 0, 80);
            }
            ?>
        </div>
        <div class="footer-text">Note : <?php echo $jarh->note; ?></div>
<?php } ?>

        <?php
        if ((int) $j === (int) $page) {
        ?>
            <table class="gridtable" width="100%">
                <tr>
                    <td class="text-center" style="border-top-style:dotted; border-right:none; border-bottom-style:dotted; border-left-style:dotted;font-size:11px">Dibukukan</td>
                    <td class="text-center" style="border-top-style:dotted; border-right:none; border-bottom-style:dotted; border-left-style:dotted;font-size:11px;">Diperiksa</td>
                    <td class="text-center" style="border-top-style:dotted; border-right-style:dotted; border-bottom-style:dotted; border-left-style:dotted;font-size:11px;">Disetujui</td>
                    <td colspan="2" style="border:none;text-align:right;font-size:11px;">
                        Jakarta, <?php echo date('d-m-Y', strtotime($jarh->tgl)); ?>
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </td>
                </tr>
                <tr>
                    <td style="border-top-style:dotted; border-right:none; border-bottom-style:dotted; border-left-style:dotted;"></td>
                    <td style="border-top-style:dotted; border-right:none; border-bottom-style:dotted; border-left-style:dotted;"></td>
                    <td style="border-top-style:dotted; border-right-style:dotted; border-bottom-style:dotted; border-left-style:dotted;"></td>
                    <td colspan="2" style="border-top:none; border-right:none; border-bottom:none; border-left:none; text-align:right;font-size:11px;">
                        <br /><br />(___________________________)<br />Penerima
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </td>
                    <td colspan="2" style="border-top:none; border-right:none; border-bottom:none; border-left:none; text-align:right;font-size:11px;">
                        <br /><br />(___________________________)<br />Kasir
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                    </td>
                </tr>
            </table>

            <span style="display:none !important;height:0px !important;">
                <div id="space"></div>
                <div id="space"></div>
                <div id="space"></div>
                <div id="space"></div>
                <div id="space"></div>
                <div id="space"></div>
                <div id="space"></div>
                <div id="space"></div>
                <div id="space"></div>
                <div id="space"></div>
                <div id="space"></div>
                <div id="space"></div>
                <div id="space"></div>
                <div id="space"></div>
                <div id="space"></div>
                <div id="space"></div>
                <?php if ((int) $page === 1) { ?>
                    <div id="space"></div>
                    <div id="space" style="padding-top:-9px;"></div>
                <?php } else { ?>
                    <div style="padding-top:-10px;"></div>
                <?php } ?>
            </span>
        <?php
        } else {
        ?>
            <span style="display:none !important;height:0px !important;">
                <div id="space"></div>
                <div id="space"></div>
                <div id="space"></div>
                <div id="space"></div>
                <div id="space"></div>
                <div id="space"></div>
                <div id="space"></div>
                <div id="space"></div>
                <div id="space"></div>
                <div id="space"></div>
                <div id="space"></div>
                <div id="space"></div>
                <div id="space"></div>
                <div id="space"></div>
                <div id="space"></div>
                <div id="space"></div>
                <div id="space"></div>
                <div id="space"></div>
                <div id="space"></div>
                <div id="space"></div>
                <div id="space"></div>
                <div id="space"></div>
                <div id="space"></div>
                <div id="space"></div>
            </span>
<?php
        }
    }
}
?>
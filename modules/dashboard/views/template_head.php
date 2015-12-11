<style>
    .headertable td {
        font-size: 11px;
        color: red;
    }
    .headertable td a{
        font-size: 11px;
        color: #000;
        font-weight: bold;
        text-decoration: none;
    }
    body{
        margin: 0px 0px 0px 0px;
        padding: 0px 0px 0px 0px; 
        font-family: Calibri, Arial, Tahoma;
        font-size: 11px;
	background:#fff;
    }
</style>
<?php
$lokasi = $this->session->userdata('lokasi');
$nama_lokasi = $this->session->userdata('nama_lokasi');
$nipp = $this->session->userdata('nipp');
$nama = $this->session->userdata('nama');
$jab  = $this->session->userdata('jab');

?>
<!--
<div style="background-image: -moz-linear-gradient(center top , #F0BC20, #FF9900);border-bottom: 1px solid #567422;">
-->
<div>
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td width="100">
                <img src="<?=base_url()?>assets/images/header.png"
                     style="padding:0px; margin:0px">
            </td>
            <td align="center">
	    </td>
            <td align="right" width="500">
                <table width="100%" border="0" class="headertable">
                  <tr>
                    <td align="right">
                        <b>INFORMASI LOGIN : <?=$nama?></b><br>
                        <a href="<?=base_url()?>index.php/dashboard/Mainindex/Logout">LOGOUT USER</a>
                    </td>
                    <td width="50" align="right">
                        <img src="<?=base_url()?>assets/images/user.png">
                    </td>
                    <td width="20"></td>
                  </tr>
                </table> 
            </td>
        </tr>        
    </table>
</div>


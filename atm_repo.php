<?php
$value = '';
PutEnv("ORACLE_SID=SVBO");
PutEnv("ORACLE_HOME=/oracle/11.2.0/");
PutEnv("TNS_ADMIN=/oracle/11.2.0/network/admin/");
$value_ex = '';
if ( $_POST['submit'] ) {
   $c=OCILogon("Username", "Password", "192.168.1.1/svfe");
   if ( ! $c ) {
      echo           die("Not connected!");
   }

	$inputValues_1 = ($_POST['ATM_Number']);
	$inputValues_2 = ($_POST['Date_s']);
	$inputValues_3 = ($_POST['Date_po']);

//echo $c;

$sql = "select decode (a.trans_type, 718, '!!! '||a.atmid||' Cut-off here !!!', a.atmid) ATM, 
a.UDATE, a.TIME, a.utrnno, substr(a.HPAN,1,6)||'****'||substr(a.HPAN,-4 ,4) PAN, a.authidresp,
decode(a.reversal,1, -1*a.REQAMT, a.REQAMT)*decode(a.currency,'944', 1, 0)*decode(a.trans_type,718, 0, 1)/100 AMT_AZN,
decode(a.reversal,1, -1*a.REQAMT, a.REQAMT)*decode(a.currency,'840', 1, 0)*decode(a.trans_type,718, 0, 1)/100 AMT_USD, a.resp
from svista.CURR_TRANS a
where  a.acq_inst='0001' and a.atmid = '".$inputValues_1."' and a.merchant=6011 and (a.PRCODE='010000' or a.trans_type=718) and a.resp in (-1,801) and a.udate >= '".$inputValues_2."' and a.udate <= '".$inputValues_3."' order by a.ATMID, a.utrnno";


$s = OCIParse($c, $sql);
OCIExecute($s, OCI_DEFAULT);
while (OCIFetch($s)) {
   $value_ex .= oci_result($s, 1) . "#" . oci_result($s, 2) . "#" . oci_result($s, 3) . "#" . oci_result($s, 4) . "#" . oci_result($s, 5) . "#" . oci_result($s, 6) . "#" . oci_result($s, 7) . "#" . oci_result($s, 8) ."#" . oci_result($s, 9) ."\n";

}
}
?>
<html>
   <form name="myform" method="post" action="afaq.php">
   <input type="text" name="ATM_Number" size="10" maxlength="6" value="<?php print $_POST['ATM_Number']; ?>" placeholder="ATM_number" />
   <input type="text" name="Date_s" size="10" maxlength="8" value="<?php print $_POST['Date_s']; ?>" placeholder="Date" />
   <input type="text" name="Date_po" size="10" maxlength="8" value="<?php print $_POST['Date_po']; ?>" placeholder="Date2" />
	<input name="submit" type=submit value="????????? ??????" />
	<textarea name="vivod" cols="70" rows="10"><?php print $value_ex; ?></textarea>
   </form>
</html>

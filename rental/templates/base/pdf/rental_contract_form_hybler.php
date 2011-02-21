<?php 
$date_format = $GLOBALS['phpgw_info']['user']['preferences']['common']['dateformat'];
$valuta_prefix = isset($config->config_data['currency_prefix']) ? $config->config_data['currency_prefix'] : '';
$valuta_suffix = isset($config->config_data['currency_suffix']) ? $config->config_data['currency_suffix'] : '';
?>
<style>
<?php include "css/contract.css"?>
</style>



<img src="http://www.nordlandssykehuset.no/getfile.php/NLSH_bilde%20og%20filarkiv/Internett/NLSH_logo_siste.jpg%20%28352x58%29.jpg" alt="Nordlanssykehuset logo" />
<h1>Melding om inn/utflytting - Hybler</h1>


<form action="" method="post">

<?php
$disabled="";
$color_checkbox = "checkbox_bg";
$checkb_in_value = true;

if (isset($_POST['preview']) )
{
	$disabled = 'disabled="disabled"';
	$color_checkbox = "";

	echo "er post";
	

}

if(isset($_POST['checkb_in'])){?><input type="hidden" name="checkb_in_hidden"  /><?php }
if(isset($_POST['checkb_out'])){?><input type="hidden" name="checkb_out_hidden"  /><?php }
if(isset($_POST['checkb_keys'])){?><input type="hidden" name="checkb_keys_hidden"  /><?php }
if(isset($_POST['checkb_janitor'])){?><input type="hidden" name="checkb_janitor_hidden"  /><?php }
if(isset($_POST['checkb_phone'])){?><input type="hidden" name="checkb_phone"  /><?php }
if(isset($_POST['checkb_HR'])){?><input type="hidden" name="checkb_HR_hidden"  /><?php }
if(isset($_POST['checkb_payroll_office'])){?><input type="hidden" name="checkb_payroll_office_hidden"  /><?php }

?>

<div class="two_column">

<dl class="left_column">
	<dt><span class="<?php echo $color_checkbox;?>"><input type="checkbox" name="checkb_in" <?php echo $disabled; if(isset($_POST['checkb_in']) || isset($_POST['checkb_in_hidden'])) {echo 'checked="checked"';}?> /></span>&nbsp Innflytting</dt>
	<dd>&nbsp</dd>
	<dt>Navn:</dt>
	<dd><?php echo $contract_party->get_first_name()." ". $contract_party->get_last_name();?></dd>
	<dt>Fnr.:</dt>
	<dd><?php echo $contract_party->get_identifier();?></dd>
	<dt>Adresse:</dt>
	<dd><?php echo $contract_party->get_address_1().", ".$contract_party->get_address_2().", ".$contract_party->get_postal_code(). " ".$contract_party->get_place()  ;?></dd>
	<dt>Tildelt bolig:</dt>
	<dd><?php echo $composite->get_name();?></dd>
</dl>


<dl class="right_column">
	<dt><span class="<?php echo $color_checkbox;?>"><input type="checkbox" name="checkb_out" <?php echo $disabled; if(isset($_POST['checkb_out'])|| isset($_POST['checkb_out_hidden'])) {echo 'checked="checked"';}?>/></span>&nbsp Utflytting</dt>
	<dd>&nbsp</dd>
	<dt>Stilling:</dt>
	<dd><?php echo $contract_party->get_title();?></dd>
	<dt>Avd.:</dt>
	<dd><?php echo $contract_party->get_department();?></dd>
	<dt>Innflytting-dato:</dt>
	<dd><?php echo date($date_format, $contract_dates->get_start_date());?></dd>
	<dt>Utflytting-dato:</dt>
	<dd><?php echo date($date_format, $contract_dates->get_end_date());?></dd>
</dl>
</div>


<div class="one_column">
<dl class="checkbox_list">
	<dt><span class="<?php echo $color_checkbox;?>"><input type="checkbox" name="checkb_keys" <?php echo $disabled; if(isset($_POST['checkb_keys']) || isset($_POST['checkb_keys_hidden'])) {echo 'checked="checked"';}?> /></span></dt>
	<dd>Lever nøkler etter utflytting til vaktmesters postkasse i postkasserommet</dd>
	<dt><span class="<?php echo $color_checkbox;?>"><input type="checkbox" name="checkb_janitor" <?php echo $disabled; if(isset($_POST['checkb_janitor']) || isset($_POST['checkb_janitor_hidden'])) {echo 'checked="checked"';}?> /></span></dt>
	<dd>Underrett vaktmester vedr. eventuelle mangler/skader</dd>
	<dt><span class="<?php echo $color_checkbox;?>"><input type="checkbox" name="checkb_phone" <?php echo $disabled; if(isset($_POST['checkb_phone']) || isset($_POST['checkb_phone_hidden'])) {echo 'checked="checked"';}?> /></span></dt>
	<dd>Har du tjenestetelefon – meld fra til personalkontoret (ikke Telenor)</dd>
</dl>
</div>

<div class="one_column">

<table>
<?php
foreach ($price_items as $item)
{
	?>
	<tr>
		<td width="80%"><?php echo $item->get_title();?></td>
		<td>Kr.:</td>
		<td align="right"><?php  echo $valuta_prefix; ?> &nbsp; <?php echo number_format($item->get_total_price()/12,2,',',' '); ?> &nbsp; <?php  echo $valuta_suffix; ?></td>
		<td>Pr.mnd.</td>
	</tr>

	<?php
}
?>
</table>
</div>


<div class="one_column">
<p>Merknader: <strong>Boligen (hybelen) skal ved flytting være ryddet og rengjort.</strong></p>
<?php if (isset($_POST['preview']) )
{
	?>
<p><?php echo $_POST['notes']?></p>
<input type="hidden" name="notes" value="<?php echo $_POST['notes']?>" />
	<?php
}
else
{
	?> <textarea rows="3" cols="" name="notes"><?php echo $_POST['notes']?></textarea> <?php
}
?> <br />
</div>

<div class="one_column">
<p>Dato: <?php echo date($date_format, time());?></p>
<table>
	<tr>
		<td align="center">
		<p class="sign">Underskrift leietaker</p>
		</td>
		<td align="center">
		<p class="sign">Underskrift vaktmester</p>
		</td>
	</tr>
</table>
</div>


<p>Kopi:</p>
<p><span class="<?php echo $color_checkbox;?>"><input type="checkbox" name="checkb_HR" <?php echo $disabled; if(isset($_POST['checkb_HR']) || isset($_POST['checkb_HR_hidden'])) {echo 'checked="checked"';}?> /></span>Personalkontoret</p>
<p><span class="<?php echo $color_checkbox;?>"><input type="checkbox" name="checkb_payroll_office"<?php echo $disabled; if(isset($_POST['checkb_payroll_office']) || isset($_POST['checkb_payroll_office_hidden'])) {echo 'checked="checked"';}?> /></span>Lønningskontoret</p>
<?php if (isset($_POST['preview']) ){ ?>
<input type="submit" value="Rediger" name="edit"> 
<input type="submit" value="Lagre som PDF" name="make_PDF"> 
<?php }else{?>

<input type="submit" value="Forhåndsvis" name="preview"> 
<?php }?>
</form>

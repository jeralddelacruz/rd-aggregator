<h2><?php echo $index_title;?></h2>
<?php
if($_POST["LoginSubmit"]){
?>
<div class="error">Authorization <strong>failed</strong>.</div>
<?php
}
?>
<form method="post" action="index.php?cmd=home">
<table class="tbl_form">
	<tr>
		<th><label for="LoginName">Username</label></th>
		<td><input type="text" name="LoginName" id="LoginName" class="text" /></td>
	</tr>
	<tr>
		<th><label for="LoginPass">Password</label></th>
		<td><input type="password" name="LoginPass" id="LoginPass" class="text" /></td>
	</tr>
</table>
<div class="submit"><input type="submit" name="LoginSubmit" value="Log In" class="button" /></div>
</form>
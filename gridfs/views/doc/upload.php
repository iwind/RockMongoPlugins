<h3><?php render_navigation($db,$collection,false); ?> &raquo; <?php hm("upload"); ?></h3>

<?php if(isset($message)): ?>
<p class="message"><?php h($message) ?></p>
<?php endif; ?>
<?php if(isset($error)): ?>
<p class="error"><?php h($error) ?></p>
<?php endif; ?>

<form method="post" enctype="multipart/form-data" action="index.php?action=@gridfs.doc.upload">
<input type="hidden" name="db" value="<?php echo xn("db"); ?>"/>
<input type="hidden" name="collection" value="<?php echo xn("collection"); ?>"/>
<table width="100%">
	<tr>
		<td><input type="file" name="file"/></td>
	</tr>
	<tr>
		<td><input type="submit" value="Upload"/></td>
	</tr>
</table>
</form>
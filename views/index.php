<div id="login">
<form method="POST" action="./">
	<select name="event_id">
	<option>----
<?php foreach( $events as $event ){ ?>
	<option value="<?php print $event['event_id']; ?>" <?php if($event['event_id']==$event_id){print 'selected';} ?>><?php print $event['name']; ?>
<?php } ?>

	</select>
	<br />
	pass:<input type="password" name="password" />
	<br />
	<br />
	<input type="submit" value="login">
</form>
</div>

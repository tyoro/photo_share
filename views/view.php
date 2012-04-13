<h2>view:<?php print "$file_name. ( $user_id )"; ?></h2>
<div class="clearfix" >
<?php foreach( $file_list as $file ){ ?>
<div class="imgbox">
<a class="image lightbox" href="./photo/<?php print $event_id.'/'.$file_id.'/m/'.$file; ?>" ><img src="./photo/<?php print $event_id.'/'.$file_id.'/s/'.$file; ?>" /></a>
<a class="caption" href="./photo/<?php print $file_id.'/'.$file; ?>">dl</a>
</div>
<?php } ?>
<script type="text/javascript" >
$('a.lightbox').lightBox();
</script>
</div>
<h2>menu</h2>
<ul>
<li><a href="./" >list</a></li>
<li><a href="./logout" >logout</a></li>
</ul>

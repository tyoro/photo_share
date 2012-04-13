<h2>utl regist</h2>
<form method="post" >
url:<?php print $request->url; ?><br />
user:<?php print $request->user; ?><br />
password:<?php print $request->password; ?><br />
<input type="hidden" name="regist" value="true" />
<input type="hidden" name="url" value="<?php print $request->url; ?>" />
<input type="hidden" name="user" value="<?php print $request->user; ?>" />
<input type="hidden" name="password" value="<?php print $request->password; ?>" />
<input type="submit" />
</form>

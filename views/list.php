<script type="text/javascript" src="http://tool.tyo.ro/yui/build/yahoo-dom-event/yahoo-dom-event.js"></script>
<script type="text/javascript" src="http://tool.tyo.ro/yui/build/element/element-min.js"></script>
<script type="text/javascript" src="http://tool.tyo.ro/yui/build/uploader/uploader-min.js"></script>
<?php if( isset( $list ) && is_array( $list ) && count( $list ) ){ ?>
<h2>list</h2>
<table>
<tr>
<th>time</th>
<th>name</th>
<th>user</th>
<th>link</th>
</tr>
<?php foreach( $list as $file ){ ?>
<tr>
<td><?php print $file['update']; ?></td>
<td><?php print $file['file_name']; ?></td>
<td><?php print $file['user_id']; ?></td>
<td><a href="./view/<?php print $file['file_id']; ?>" >view</a>
&nbsp;
<a href="./dl/<?php print $file['file_id']; ?>" >dl</a>
<?php if( isset($file['file_size']) && !empty($file['file_size']) ){ ?>
&nbsp;(&nbsp;<?php print $util->get_file_unit($file['file_size']); ?>&nbsp; )
<?php } ?>
</td>
</tr>
<?php } ?>
</table>
<br/>
<?php }else{ ?>
file not found... <br/>
<?php } ?>
<?php if( isset( $url_list ) && is_array( $url_list ) && count( $url_list ) ){ ?>
<h2>URL</h2>
<table>
<tr>
<th>time</th>
<th>url</th>
<th>user</th>
</tr>
<?php foreach( $url_list as $url){ ?>
<tr>
<td><?php print $url['update']; ?></td>
<td><a href="<?php print $url['url']; ?>" ><?php print $url['url']; ?></a></td>
<td><?php print $url['user_id']; ?></td>
</tr>
<?php } //foreach ?>
</table>
<br/>
<?php } //if ?>
<a href="./url/set">add url</a>
<h2>zip upload</h2>
<form method="POST" action="./file_execute" id="form" >
user id:<input type="edit" name="id" /><br/>
delete pass:<input type="password" name="password" /><br />
<input type="hidden" name="name" />
<input type="hidden" name="tmp_name" />
</form>
<div id="uploaderUI" style="width:100px;height:40px;margin-left:5px;float:left"></div>
	<div class="uploadButton" style="float:left">
		<a class="rolloverButton" href="#" onClick="upload(); return false;"></a>
	</div>
	<div class="clearButton" style="float:left">
		<a class="rolloverButton" href="#" onClick="handleClearFiles(); return false;"></a>
	</div>
</div>
<br/>
<br/>
<br/>
<div id="fileProgress" style="border: black 1px solid; width:300px; height:40px;float:left">
	<div id="fileName" style="text-align:center; margin:5px; font-size:15px; width:290px; height:25px; overflow:hidden">
	</div>
	<div id="progressBar" style="width:300px;height:5px;background-color:#CCCCCC">
	</div>
</div>
<script type="text/javascript">
YAHOO.widget.Uploader.SWFURL = "http://tool.tyo.ro/yui/build/uploader/assets/uploader.swf";
var uploader = new YAHOO.widget.Uploader( "uploaderUI", "img/yui/selectFileButton.png" );

uploader.addListener('contentReady', handleContentReady);
uploader.addListener('fileSelect',onFileSelect)
uploader.addListener('uploadStart',onUploadStart);
uploader.addListener('uploadProgress',onUploadProgress);
uploader.addListener('uploadCancel',onUploadCancel);
uploader.addListener('uploadComplete',onUploadComplete);
uploader.addListener('uploadCompleteData',onUploadResponse);
uploader.addListener('uploadError', onUploadError);


function handleContentReady () {
    // Allows the uploader to send log messages to trace, as well as to YAHOO.log
	uploader.setAllowLogging(true);
		
	// Restrict selection to a single file (that's what it is by default,
	// just demonstrating how).
	uploader.setAllowMultipleFiles(false);
	
	// New set of file filters.
	var ff = new Array({description:".zip", extensions:"*.zip"});
		                   
	// Apply new set of file filters to the uploader.
	uploader.setFileFilters(ff);
}

var fileID;
function onFileSelect(event) {
	for (var item in event.fileList) {
	    if(YAHOO.lang.hasOwnProperty(event.fileList, item)) {
			YAHOO.log(event.fileList[item].id);
			fileID = event.fileList[item].id;
		}
	}
	uploader.disable();
	
	var filename = document.getElementById("fileName");
	filename.innerHTML = event.fileList[fileID].name;
	
	var progressbar = document.getElementById("progressBar");
	progressbar.innerHTML = "";
}

function upload() {
	if (fileID != null) {
		uploader.upload(fileID, "http://tool.tyo.ro/photo_share/file_upload", 'POST' );
		fileID = null;
	}
}

function handleClearFiles() {
	uploader.clearFileList();
	uploader.enable();
	fileID = null;
	
	var filename = document.getElementById("fileName");
	filename.innerHTML = "";
	
	var progressbar = document.getElementById("progressBar");
	progressbar.innerHTML = "";
}

function onUploadProgress(event) {
	prog = Math.round(300*(event["bytesLoaded"]/event["bytesTotal"]));
  	progbar = "<div style=\"background-color: #f00; height: 5px; width: " + prog + "px\"/>";

	var progressbar = document.getElementById("progressBar");
	progressbar.innerHTML = progbar;
}

function onUploadComplete(event) {
	uploader.clearFileList();
	uploader.enable();
		
	progbar = "<div style=\"background-color: #f00; height: 5px; width: 300px\"/>";
	var progressbar = document.getElementById("progressBar");
	progressbar.innerHTML = progbar;
}

function onUploadStart(event) {	
}

function onUploadError(event) {
}
	
function onUploadCancel(event) {
}
	
function onUploadResponse(event) {
eval('jsondata='+event.data);
$('#form input[name="name"]').val( jsondata.name );
$('#form input[name="tmp_name"]').val( jsondata.tmp_name );
console.log($('#form').eq(0).submit());


}

</script>
<div style="clear:left;"></div>
<br/>
<br/>
<br/>
<br/>
<br/>
<br/>
1GB以上のファイルの場合はタイムアウトの恐れあり、、、
<h2>menu</h2>
<a href="./logout" >logout</a>

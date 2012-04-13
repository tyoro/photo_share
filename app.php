<?php

ini_set("gd.jpeg_ignore_warning", 1);
ini_set("max_execution_time",300);

	include_once 'conf.php';
	include_once 'dao.php';
	include_once 'myFitzgerald.php';
	include_once 'lib/adodb5/adodb.inc.php';
	include_once 'lib/adodb5/adodb-pear.inc.php';

function is_img($file){
    if (!(file_exists($file) && ($type=exif_imagetype($file)))) return false;
	/*if (!file_exists($file)){return false;}
	
	$type=@getimagesize($file);
	if( $type === FALSE || !($type = $type[2] ) ){return false;}
	*/
    switch ($type) {
        case IMAGETYPE_GIF:
            return 'gif';
        case IMAGETYPE_JPEG:
            return 'jpg';
        case IMAGETYPE_PNG:
            return 'png';
        default:
            return false;
    }
}

function create_file( $file_name, $photo_dir_name, $width, $height, $to_dir ){
	list($image_w, $image_h,$type) = getimagesize($photo_dir_name.$file_name);
	switch( $type ){
		case IMAGETYPE_GIF:
			$image = imagecreatefromgif($photo_dir_name.$file_name);
			break;
		case IMAGETYPE_JPEG:
			$image = imagecreatefromjpeg($photo_dir_name.$file_name);
			break;
		case IMAGETYPE_PNG:
			$image = imagecreatefrompng($photo_dir_name.$file_name);
			break;
		default:
			return;
	}
	$canvas = ImageCreateTrueColor($width, $height);
	imagefill($canvas , 0 , 0 , 0xFFFFFF);

	$wRate = $image_w / $width;
	$hRate = $image_h / $height;

	if( $wRate > $hRate )
	{
		$newWidth  = $width;
		$newHeight = ($image_h / $image_w ) * $newWidth;
	}
	else
	{
		$newHeight = $height;
		$newWidth  = ( $image_w / $image_h ) * $newHeight;
	}
	$newX = ( $width - $newWidth ) / 2 + 0.5;
	$newY = ( $height - $newHeight ) / 2 + 0.5;

	imagecopyresampled($canvas,
		$image,
		$newX,$newY,0,0,
		$newWidth,$newHeight,$image_w,$image_h);
	imagejpeg($canvas, $photo_dir_name. $to_dir. '/' .$file_name );
	imagedestroy($canvas);
	
	imagedestroy($image);
}

function create_files( $file_name, $photo_dir_name, $to_imgs ){
	list($image_w, $image_h,$type) = getimagesize($photo_dir_name.$file_name);
	switch( $type ){
		case IMAGETYPE_GIF:
			$image = imagecreatefromgif($photo_dir_name.$file_name);
			break;
		case IMAGETYPE_JPEG:
			$image = imagecreatefromjpeg($photo_dir_name.$file_name);
			break;
		case IMAGETYPE_PNG:
			$image = imagecreatefrompng($photo_dir_name.$file_name);
			break;
		default:
			return;
	}
	foreach( $to_imgs as $to_img ){
		$width = $to_img['width'];
		$height= $to_img['height'];
		$to_dir = $to_img['to_dir'];

		$canvas = ImageCreateTrueColor($width, $height);
		imagefill($canvas , 0 , 0 , 0xFFFFFF);

		$wRate = $image_w / $width;
		$hRate = $image_h / $height;

		if( $wRate > $hRate )
		{
			$newWidth  = $width;
			$newHeight = ($image_h / $image_w ) * $newWidth;
		}
		else
		{
			$newHeight = $height;
			$newWidth  = ( $image_w / $image_h ) * $newHeight;
		}
		$newX = ( $width - $newWidth ) / 2 + 0.5;
		$newY = ( $height - $newHeight ) / 2 + 0.5;

		imagecopyresampled($canvas,
			$image,
			$newX,$newY,0,0,
			$newWidth,$newHeight,$image_w,$image_h);
		imagejpeg($canvas, $photo_dir_name. $to_dir. '/' .$file_name );
		imagedestroy($canvas);
	}
	imagedestroy($image);
}


class PhotoShare extends MyFitzgerald {
	public static $page_title = "photo share";
	public static $msg = '';

	public function index( $id ){
		if( $this->isLoggedIn() ){ 
			return $this->render( 'list',
				array( 
					'list'=>$this->db->file->getFileList($this->session->event),
					'url_list'=>$this->db->url->getURLList($this->session->event),
				)
			);
		}else{
			return $this->render('index',array('events'=>$this->db->event->getEventList(),'event_id'=>$id),array('util'));
		}
	}

	private function to_log( $str ){
		ob_start();
		var_dump( $str);
		$dump = ob_get_contents();

		$fhn = fopen("./file/log.txt","a");
		fwrite($fhn,$dump);
		fclose($fhn);
	}
			
	public function file_upload(){
		$filedata = $_FILES["Filedata"]; 
		move_uploaded_file($filedata["tmp_name"], '.'.$filedata["tmp_name"] );
		$filedata["tmp_name"] = '.'.$filedata["tmp_name"];
			
		return json_encode( $filedata );
	}

	public function file_execute(){
		$zip_mimes = array( 'application/x-zip-compressed', 'application/zip' );
		if( $this->isLoggedIn() ){
			$event_id = $this->session->event;
			if( isset($this->request->name) && isset( $this->request->tmp_name)  && isset($this->request->id) && isset($this->request->password) ){
				$mime_type = mime_content_type($this->request->tmp_name);

				if( in_array($mime_type,$zip_mimes) ){
					if(is_file($this->request->tmp_name) ){
						$file_id = $this->db->file->setFile( $this->session->event , $this->request->name, filesize($this->request->tmp_name), $this->request->id, $this->request->password );

						$zip_name = './file/'.$event_id.'/'.$file_id.'.zip';
						$photo_dir_name = './photo/'.$event_id.'/'.$file_id.'/';

						if( !is_dir( './file/'.$event_id.'/' ) ){
							mkdir('./file/'.$event_id.'/' );
						}
						if( !is_dir( './photo/'.$event_id.'/') ){
							mkdir('./photo/'.$event_id.'/' );
						}
						if( !is_dir( $photo_dir_name) ){
							mkdir( $photo_dir_name );
						}

						rename($this->request->tmp_name, $zip_name);
						$zip = new ZipArchive();
						$rtn = $zip->open( $zip_name );
						if ($rtn !== true) {
							exit('no zip file.');
						}
						$zip->extractTo( $photo_dir_name );
						$zip->close();

						//chmod( $photo_dir_name, 0777 );

						$res_dir = opendir( $photo_dir_name );
						if( !$res_dir ){
							exit('dir open error.');
						}
						$width = 100;
						$height= 75;
						$names = array();
						while( $name = readdir(  $res_dir ) ){
							if( $name != '.' && $name != '..' ){
								if( is_dir( $photo_dir_name.$name) ){
									$res_sub_dir = opendir( $photo_dir_name.$name );
									while( $sub_file = readdir(  $res_sub_dir ) ){
										if( $sub_file != '.' && $sub_file != '..' ){
											rename($photo_dir_name.$name.'/'.$sub_file,$photo_dir_name.$sub_file);
											$names[] = $sub_file;
										}
/**
 *  
 */
									}
									closedir( $res_sub_dir );
									rmdir( $photo_dir_name.$name );
								}else{
									$names[] = $name;
								}
							}
						}
						closedir( $res_dir );
						mkdir( $photo_dir_name.'s/' );
						mkdir( $photo_dir_name.'m/' );
						foreach( $names as $file_name){
							if( is_img( $photo_dir_name.$file_name ) ){
								create_files( $file_name, $photo_dir_name, array(
									array( 'width' => 100, 'height' => 75, 'to_dir' => 's'),
									array( 'width' => 500, 'height' => 375, 'to_dir' => 'm')
								) );
								//create_file( $file_name, $photo_dir_name, 100, 75, 's' );
								//create_file( $file_name, $photo_dir_name, 500, 375, 'm' );
							}
						}
					}else{
						exit('file not found.');
					}
				}else{
					exit( 'type error:'.$mime_type );
				}
				return $this->redirect('/view/'.$file_id);
			}else{
				exit('input error.');
			}
		}
		return $this->redirect('/');
	}

	public function view( $id ){
		if( $this->isLoggedIn() ){
			$event_id = $this->session->event;
			//TODO:chdir
			$res_dir = opendir( './photo/'.$event_id.'/'.$id.'/s/');
				if( !$res_dir ){
				exit('dir open error.');
			}
			$file_list = array();
			while( $file_name = readdir( $res_dir ) ){
				if( $file_name != '.' && $file_name != '..' ){
					$file_list[] = $file_name;
				}
			}
			closedir( $res_dir );
			sort($file_list);
			$row = $this->db->file->getFile( $event_id, $id );
			return $this->render( 'view', array( 'file_list'=>$file_list,'file_id'=>$id,'event_id'=>$event_id, 'file_name'=>$row['file_name'],'user_id'=>$row['user_id'] ) );
		}
	}

	public function file_download( $file ){
		if( $this->isLoggedIn() ){
			$msie = false;
			$safari = false;
			$opera = false;
			$useragent = getenv("HTTP_USER_AGENT");
			if(ereg("Mozilla/4.0 \(compatible; MSIE", $useragent)){
				$msie = true;
			}
			if(ereg("Safari", $useragent)){
				$safari = true;
			}
			if(ereg("Opera", $useragent)){
				$opera = true;
			}

			$event_id = $this->session->event;
			$zip_name = './file/'.$event_id.'/'.$file.'.zip';
			$row = $this->db->file->getFile( $event_id, $file );

			$download_file_name = $row['file_name'];
			$download_file_name = str_replace(' ', '_', $download_file_name);
			//$download_file_name = '=?UTF-8?B?' . base64_encode($row['name']) . '?=';
			
			apache_setenv('no-gzip', '1');

			//キャッシュ無効化
			header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
			header("Last-Modified: ".gmdate("D,d M Y H:i:s")." GMT");

			if ($msie == true) {
				//IEの場合
				//SJISにファイル名を変換
				$download_file_name = mb_convert_encoding($download_file_name, "SJIS-win", "UTF-8");
				Header("Content-Disposition: attachment; filename=" . $download_file_name);
				Header('Pragma: private');
				Header('Cache-Control: private');
				Header("Content-Type: application/octet-stream-dummy");
			} else {
				//IE以外
				 if ($safari == true) {
					//Safariの場合は全角文字が全て化けるので、何かしら固定のファイル名にして回避
					$download_file_name = basename($zip_name);
				}
				Header("Content-Disposition: attachment; filename=" . $download_file_name);
				//Header("Content-Type: application/octet-stream; name=" . $download_file_name);
				Header("Content-Type: application/zip; name=" . $download_file_name);
			}
			Header("Content-Length: ". filesize($zip_name));
			
			set_time_limit(0);
			ob_flush();
			flush();

			$handle = fopen($zip_name, "r");
			while(!feof($handle)){
				print fread($handle, 1024*1024);
				ob_flush();
				flush();
			}
			fclose ($handle);
		}
	}

	public function photo_download( $file, $name ){
		if( $this->isLoggedIn() ){
			$event_id = $this->session->event;
			$file_name = './photo/'.$event_id.'/'.$file.'/'.$name;
			$row = $this->db->file->getFile( $event_id, $file );

			$mime_type = mime_content_type($file_name);

			header('Content-Type: '.$mime_type);
			header('Content-Length: '.filesize($file_name));
			readfile($file_name);
		}
	}

	//url
	public function url_set(){
		if( $this->isLoggedIn() ){ 
			if( $this->request->regist == 'true' ){
				$this->db->url->setURL( $this->session->event, $this->request->url, $this->request->user, $this->request->password );
				return $this->redirect('/');
			}else{
				if( strlen($this->request->url) && strlen($this->request->user) ){
					return $this->render('url/check');
				}else{
					return $this->render('url/regist');
				}
			}
		}else{
			return $this->redirect('/');
		}
	}

	//login
	public function logout(){
		if( $this->isLoggedIn() ){ session_destroy(); }
		return $this->redirect('/');
	}

	public function login( ){
		if( !$this->isLoggedIn() ){ 
			$event = $this->db->event->loginEvent($this->request->event_id,$this->request->password);

			if( $event !== false ){
				$this->session->event = $this->request->event_id;
			}
		}
		return $this->redirect('/');
	}

	private function isLoggedIn() {
		return !is_null($this->session->event);
	}
}

$app = new PhotoShare( $conf );

//routing
$app->get('/','index');
$app->get('/:id','index',array('id'=>'\d+'));
$app->get('/logout','logout');
$app->post('/','login');
$app->post('/file_upload','file_upload');
$app->post('/file_execute','file_execute');

$app->get('/url/set', 'url_set');
$app->post('/url/set', 'url_set');

$app->get('/view/:id','view',array('id'=>'\d+'));
$app->get('/dl/:file','file_download',array('file'=>'\d+'));
$app->get('/photo/:file/:name','photo_download',array('file'=>'\d+','name'=>'[\w\d\-]+\.[\w]+'));

$app->run();

class myUtilHelper{
	public function get_file_unit($size, $num = 1) {
		if (strlen($size) > 12) {
			$res = round($size / 1000000000000, $num) . "TB";
		} elseif (strlen($size) > 9) {
			$res = round($size / 1000000000, $num) . "GB";
		} elseif (strlen($size) > 6) {
			$res = round($size / 1000000, $num) . "MB";
		} elseif (strlen($size) > 3) {
			$res = round($size / 1000, $num) . "KB";
		} else {
			$res = $size . "B";
		}
		return $res;
	}
}

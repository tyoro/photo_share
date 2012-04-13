<?php
//setlocale(LC_ALL, "ja_JP.UTF-8");

class dao_base{
	var $_conn = null;

	public function __construct( $conn ){
		$this->_conn = $conn;
	}

	public function qs( $str ){
		return $this->_conn->qstr( $str );
	}

	protected function onDebug(){ $this->_conn->debug = 1; }
	protected function offDebug(){ $this->_conn->debug = 0; }

}

class dao_event extends dao_base{
	function getEventList(){
		$sql = "SELECT * FROM event";
		return $this->_conn->GetArray($sql);
	}
	function loginEvent( $id, $pass ){
		$sql = "SELECT event_id FROM event WHERE event_id = ".$this->qs($id)." AND password = ".$this->qs($pass);
		return $this->_conn->GetOne($sql);
	}
}

class dao_file extends dao_base{
	function getFileList( $event_id ){
		$sql = "SELECT * FROM file WHERE event_id = ".$this->qs($event_id);
		return $this->_conn->GetArray($sql);
	}

	function setFile( $event_id, $file_name, $file_size, $user_id, $password ){
	   	$this->_conn->AutoExecute( 'file',array( 'event_id'=>$event_id, 'file_name'=>$file_name, 'file_size'=>$file_size, 'user_id'=>$user_id, 'password'=>$password ), DB_AUTOQUERY_INSERT );
		return $this->_conn->Insert_ID();
	}

	function getFile( $event_id, $file_id ){
		$sql = "SELECT * FROM file WHERE event_id = ".$this->qs($event_id)." AND file_id=".$this->qs($file_id);
		$row = $this->_conn->GetRow($sql);
		return $row;
	}
}

class dao_url extends dao_base{
	function getURLList( $event_id ){
		$sql = "SELECT * FROM url WHERE event_id = ".$this->qs($event_id);
		return $this->_conn->GetArray($sql);
	}

	function setURL( $event_id, $url, $user_id, $password ){
	   	print $this->_conn->AutoExecute( 'url', array( 'event_id'=>$event_id, 'url'=>$url, 'user_id'=>$user_id, 'password'=>$password ), DB_AUTOQUERY_INSERT );
		return $this->_conn->Insert_ID();
	}

	function getURL( $event_id, $url_id ){
		$sql = "SELECT * FROM url WHERE event_id = ".$this->qs($event_id)." AND url_id=".$this->qs($url_id);
		return $this->_conn->GetRow($sql);
	}
}

<?php namespace pouu69\LaravelFacebookUpload;


class LaravelFacebookSession{
	protected $FB_SESSION = '';

	public function __construct(){

	}

	public function get(){
		return $this->FB_SESSION;
	}

	public function set($sessionName){
		$this->FB_SESSION = session($sessionName);
	}
}
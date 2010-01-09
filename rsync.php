<?php

uses('execute');

if(!defined('RSYNC_PATH')) define('RSYNC_PATH', 'rsync');

class Rsync
{
	public $context;
	
	public function recvFile($remote, $local)
	{
		if(!($info = parse_url($remote)))
		{
			return;
		}
		$args = array();
		if($info['scheme'] == 'rsync+ssh')
		{
			if(isset($info['user']))
			{
				$user = (isset($this->source['user']) ? $this->source['user'] . '@' : '');
			}
			$args[] = $user . $this->source['host'] . ':' . $this->source['path'];
		}
		else
		{
			$args[] = 'rsync://' . $this->source['host'] . '/' . $this->source['path'];		
		}
			
	}

	public function sendFile($local, $remote)
	{
	}
}


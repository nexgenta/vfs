<?php

if(!defined('GET_IPLAYER_PATH')) define('GET_IPLAYER_PATH', '/usr/bin/get_iplayer');
if(!defined('FLVSTREAMER_PATH')) define('FLVSTREAMER_PATH', '/usr/bin/flvstreamer');

uses('execute');

/* iPlayer URLs take the form:
 *
 * iplayer://episode/<pid>.{mov|flv|rdf|jpg}
 */

class iPlayer
{
	public $context;
	
	public function recvFile($remote, $local)
	{
		if(!($info = parse_url($remote)))
		{
			return;
		}
		if(isset($info['path']))
		{
			$info = array_merge($info, pathinfo($info['path']));
		}
		if(!isset($info['filename']))
		{
			trigger_error('iPlayer: No PID specified', E_USER_WARNING);
			return false;
		}
		if(!isset($info['extension']))
		{
			trigger_error('iPlayer: No file type (extension) specified', E_USER_WARNING);
			return false;
		}
		if(is_dir($local))
		{
			$localdir = $local;
			$local .= '/' . $info['basename'];
		}
		else
		{
			$localdir = dirname($local);
		}
		if($info['extension'] == 'jpg')
		{
			return copy('http://www.bbc.co.uk/iplayer/images/episode/' . $info['filename'] . '_832_468.jpg', $local);
		}
		if($info['extension'] == 'rdf')
		{
			return copy('http://www.bbc.co.uk/programmes/' . $info['filename'] . '.rdf', $local);
		}
		if($info['extension'] == 'flv')
		{
			$tmpfile = $localdir . '/.iplayer-tmp-' . $info['filename'] . '.flv';
			if(!file_exists($tmpfile))
			{
				$args = array();
				$args[] = GET_IPLAYER_PATH;
				$args[] = '--flvstreamer';
				$args[] = FLVSTREAMER_PATH;
				$args[] = '--modes=flashhd1,flashhd2,flashvhigh,flashhigh';
				$args[] = '--quiet';
				$args[] = '--nocopyright';
				$args[] = '--nopurge';
				$args[] = '--file-prefix=.iplayer-tmp-' . $info['filename'];
				$args[] = '--pid=' . $info['filename'];
				$args[] = '--force';
				$args[] = '--raw';
				$args[] = '-o';
				$args[] = $localdir;
				$result = execute(GET_IPLAYER_PATH, $args, false);
				if(!file_exists($tmpfile))
				{
					trigger_error('get_iplayer failed to download ' . $info['basename'], E_USER_NOTICE);
					return false;
				}
			}
			if(!rename($tmpfile, $local))
			{
				return false;
			}
			return true;
		}
		print_r($info);
		die();
	}
}
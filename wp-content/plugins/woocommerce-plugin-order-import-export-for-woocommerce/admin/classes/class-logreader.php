<?php
/**
 * Log reading section of the plugin
 *
 * @link       
 *
 * @package  Wt_Import_Export_For_Woo 
 */
if (!defined('ABSPATH')) {
    exit;
}
if(!class_exists('Wt_Import_Export_For_Woo_Basic_Logreader')){
class Wt_Import_Export_For_Woo_Basic_Logreader
{
	private $file_path='';
	private $file_pointer=null;
	private $mode='';
	public function __construct()
	{
		
	}
	public function init($file_path, $mode="r")
	{
		$this->file_path=$file_path;
		$this->mode=$mode;
		$this->file_pointer=@fopen($file_path, 'r');
	}
	public function close_file_pointer()
	{
		if($this->file_pointer!=null)
		{
			fclose($this->file_pointer);
		}
	}

	public function get_full_data($file_path)
	{
		$out=array(
			'response'=>false,
			'data_str'=>'',
		);
		$this->init($file_path);
		if(!is_resource($this->file_pointer))
		{
			return $out;
		}
		$data=fread($this->file_pointer, filesize($file_path));

		$this->close_file_pointer();

		$out=array(
			'response'=>false,
			'data_str'=>$data,
		);
		return $out;
	}
	
	/**
	*	Read log file as batch
	*	@param 		string  	path of file to read
	*	@param 		int  		offset in bytes. default 0
	*	@param 		int  		total row in a batch. default 50
	*	@return 	array  		response, next offset, data array, finished or not flag
	*/
	public function get_data_as_batch($file_path, $offset=0, $batch_count=50)
	{
		$out=array(
			'response'=>false,
			'offset'=>$offset,
			'data_arr'=>array(),
			'finished'=>false, //end of file reached or not
		);
		$this->init($file_path);
		if(!is_resource($this->file_pointer))
		{
			return $out;
		}

		fseek($this->file_pointer, $offset);
		$row_count=0;
		$next_offset=$offset;
		$finished=false;
		$data_arr=array();
		while(($data=fgets($this->file_pointer))!==false)
		{
			$data=maybe_unserialize($data);
			if(is_array($data))
			{
				$data_arr[]=$data;
				$row_count++;
				$next_offset=ftell($this->file_pointer);
			}
			if($row_count==$batch_count)
			{
				break;
			}
		}
		if($next_offset==filesize($file_path))
		{
			$finished=true;
		}
		$this->close_file_pointer();

		$out=array(
			'response'=>true,
			'offset'=>$next_offset,
			'data_arr'=>$data_arr,
			'finished'=>$finished,
		);
		return $out;
	}
}
}
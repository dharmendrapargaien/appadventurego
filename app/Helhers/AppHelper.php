<?php

namespace App\Helpers;

class AppHelper
{
	
	/**
	* Function sets the flash message with type
	* @param string $message [contains message to be displayed]
	* @param string $type    [contains type of message]
	*/
	public static function setFlashMessage ($message, $type = 'success'){
		
		if(session()->has('flashMessages')){
			$data = session('flashMessages');
		}else{
			$data = [
				'success' => [],
				'info'    => [],
				'error'   => [],
				'warning' => [],
			];
		}

		$data[$type][] = $message;

		session()->flash('flashMessages', $data);
	}
    
    /**
     * This function is for testing purpose only
     * Remove it in future
     */
    public static function test()
    {
        $sql = \DB::getQueryLog();
        echo "<pre>";
        print_r($sql);
        echo "</pre>";
        die("here");
    }
}
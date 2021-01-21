<?php
 
namespace App\Exceptions;
 
use Exception;
use Log;

class ProException extends Exception
{

	public $customErrors = [];
	/**
	 * Report or log an ProException.
	 *
	 * This is the function to generate Pro Exceptions.
	 *
	 * @param  \Exception  $exception
	 * @return void
	 */
	public function __construct($field_id, $error_id = '', $data = [])
	{
		if(!is_array($data)) {
			$data = [$data];
		}
		if(is_array($field_id))
		{
			$errorsBatch = $field_id;
			if(count($errorsBatch))
			{
				foreach($errorsBatch as $field_id => $fieldErrors)
				{
					if(!isset($this->customErrors[$field_id])) {
						$this->customErrors[$field_id] = [];
					}
					$this->customErrors[$field_id] = array_merge($this->customErrors[$field_id], $fieldErrors);
				}
			}
			parent::__construct(json_encode($this->customErrors));
		}
		else
		{
			if(!isset($this->customErrors[$field_id])) {
				$this->customErrors[$field_id] = [];
			}
			
			$this->customErrors[$field_id][$error_id] = 1;
			parent::__construct($error_id);
		}
		Log::error('ProException : ' . var_export($this->customErrors, true) . "\n" . var_export($field_id, true) . "\n" . var_export($data, true));
	}

	/**
	 * Report the exception.
	 *
	 * @return void
	 */
	public function report()
	{

	}
 
	/**
	 * Render the exception into an HTTP response.
	 *
	 * @param  \Illuminate\Http\Request
	 * @return \Illuminate\Http\Response
	 */
	public function render($request)
	{
		$retval = [
			'data'     => [],
			'is_error' => true,
			'errors'   => $this->customErrors,
		];

		return response()->json($retval);
	}
}

 ?>
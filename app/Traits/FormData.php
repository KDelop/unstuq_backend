<?php
namespace App\Traits;
use Illuminate\Http\Request;
use Validator;
use Illuminate\Validation\Rule;
use App\Exceptions\ProException;
trait FormData{
	public static function throw($field_id, $error_id, $internalMessage = array()){
		throw new Exceptions\ProException($field_id, $error_id, $internalMessage);
	}

	public static function throwBatch($validatorErrors = array()){
		$myErrors = [];
		foreach($validatorErrors as $field_id => $fieldErrors){
			$myErrors[$field_id] = [];
			foreach($fieldErrors as $fieldError_id){
				$myErrors[$field_id] = [$fieldError_id];
			}
		}
		// $retval = [
		// 	'data'     => [],
		// 	'is_error' => true,
		// 	'errors'   => $myErrors,
		// ];
		// return $retval;
		// throw new get_parsed_validation_error_response($validator);
		throw new ProException($myErrors);
	}

	public function publishDie($data = []){
		return response()->json([
			'data'     => $data,
			'is_error' => false,
			'errors'   => [],
		]);
	}

	public static $messages = [
		'required' => ':attribute.required',
		'email'    => ':attribute.invalid',
		'min'      => ':attribute.min',
		'max'      => ':attribute.max',
		'same'     => ':attribute.notsame.:other',
		'size'     => ':attribute.notsize.:size',
		'between'  => ':attribute.notbetween.:min.:max',
		'in'       => ':attribute.notin',
		'exists'   => ':attribute.notexists',
		'unique'   => ':attribute.taken.',
	];

	public static $numeric_conversion = [

	];
    
    public function preparePostData(Request $request, $mandatory_fields = [], $optional_fields = array(), $requireLogin = true){
    	if($request->searchType=='discover'){
    		return true;
    	}
    	// return json_encode(['request'=>$request->all()]);
        // $this->attributes['created_at'] = Carbon::parse($date);
        $postData = self::postData($request, $requireLogin);
		self::strict($postData, $mandatory_fields, $optional_fields);
		return $postData;
    }

    public static function postData(Request $request, $requireLogin = true){
		$data = $request->all();
		// $data = except(['_token']);
		// if($requireLogin === true){
		// 	self::require_login();
		// }
		// $user = Auth::user();
		// if($user){
		// 	$data['client_id'] = $user->client_id;
		// }
		return $data;
	}

	public static function require_login($roles = array('*')){
		$user = self::user();
		if(!$user){
			self::throw('global', 'not.loggedin', 'You are not loggedin.');
		}
		if(is_string($roles)){
			$roles = [$roles];
		}
		if(is_array($roles) && count($roles) > 0){
			if($roles[0] == '*'){
				return true;
			}
			if(!in_array($user->role, $roles)){
				self::throw('global', 'not.allowed', 'You do not have enough permissions to perform this action.');
			}
		}
		return true;
	}

	public static function strict($data, $mandatory_fields, $optional_fields = array()){
		$validator = self::perform($data, $mandatory_fields, $optional_fields);
		if($validator->fails()){
            $errors = $validator->errors();
			self::throwBatch($errors->toArray());
		}else{
			return true;
		}
	}


	public static function perform($data, $mandatory_fields, $optional_fields = array())
	{
		$rules_master = [];
		$rules = [];
		if(is_array($mandatory_fields) && count($mandatory_fields) > 0){
			foreach($mandatory_fields as $field_name){
				if(!isset($rules_master[$field_name])){
					$rules[$field_name] = 'required';
				}else if(is_string($rules_master[$field_name])){
					$rules[$field_name] = 'required|'.$rules_master[$field_name];
				}else if (is_array($rules_master[$field_name])){
					$rules[$field_name] = array_merge(['required'], $rules_master[$field_name]);
				}
			}
		}

		if(is_array($optional_fields) && count($optional_fields) > 0){
			foreach($optional_fields as $field_name){
				if(!isset($rules_master[$field_name])){
					$rules[$field_name] = 'nullable';
				}else if(is_string($rules_master[$field_name])){
					$rules[$field_name] = 'nullable|'.$rules_master[$field_name];
				}else if (is_array($rules_master[$field_name])){
					$rules[$field_name] = array_merge(['nullable'], $rules_master[$field_name]);
				}
			}
		}

		// Custom Rules
		if(isset($rules['searchType'])){
			$rules['searchType'] = [
				'required',
				Rule::in(['group', 'solo']),
			];
		}
		if(isset($rules['providers'])){
			$rules['providers'] = [
				'required',
				Rule::in(['walmart', 'ebay', 'google', 'amazon']),
			];
		}

		if(isset($rules['searchOptions'])){
			$rules['searchOptions'] = [
				'required',
				'array',
				'min:2',
			];
			$rules['searchOptions.*.name'] = [
				'required',
			];
			$rules['searchOptions.*.description'] = [
				'required',
			];
			// $rules['searchOptions.*.image'] = [
			// 	'required',
			// 	'image',
			// 	'mimes:jpeg,png,jpg,gif,svg',
			// 	'max:2048',
			// ];
		}

		// foreach($data as $field_name => $field_value){
		// 	if(in_array($field_name, self::$numeric_conversion)){
		// 		$data[$field_name] = $field_value + 0;
		// 	}
		// }
		// dd($rules);
		// $validator = Validator::make($data, $rules, self::$messages);
		$validator = Validator::make($data, $rules, []);
		// dd($validator->errors());
		return  $validator;
		// print_r(self::$messages);die();
	}
}
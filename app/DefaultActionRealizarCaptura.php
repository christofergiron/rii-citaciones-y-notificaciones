<?php

namespace App;

use Validator;
use App\WorkflowTrait;

Class DefaultActionRealizarCaptura implements iAction {

    use WorkflowTrait;
    private $response;
    public function __construct()
    {
        $this->log = new \Log;
        $this->response = new \stdClass;
        $this->response->code = 200;
        $this->response->message = "";
    }

	public function validate(Array $arr) {
       $validator = Validator::make($arr   , [
         "token" => "required",
         "user_email" => "required",
         "workflow_type" => "required",
         "action" => "required",
         "object_id" => "required|numeric|exists:capturas,id",
         "workflow_type" => "required"
       ]);

       if ($validator->fails()) {
         $this->response->code = 403;
         $this->response->message = $validator->errors();
         return $this->response;
       }

       return $this->response;
	}

    public function apply_transition(Array $arr) {
        // apply workflow transition
        $this->log::alert('inside default action @ DefaultActionRealizarCaptura');
        $res = $this->apply_workflow_transition($arr);

        if (!isset($res)) {
            return null;
        }

        return $res;

    }

}

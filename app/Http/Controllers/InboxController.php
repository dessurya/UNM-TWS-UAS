<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Inbox;

use Validator;

class InboxController extends Controller
{
    private function validator($input)
    {
        $message = [];
        $rule = [
            'name' => 'required',
            'email' => 'required',
            'phone' => 'required',
            'subjeck' => 'required',
            'message' => 'required',
        ];
        $valid = Validator::make($input,$rule,$message);
        if ($valid->fails()) {
            return [
                'res' => true,
                'message' => 'fail',
                'invalid' => $valid->getMessageBag()->toArray(),
            ];
        }else{ return true; }
    }

    public function list(Request $httpRequest)
    {
        $condition = [];
        if (isset($httpRequest->name) AND !empty($httpRequest->name)) { $condition['name'] = '%'.$httpRequest->name.'%'; }
        if (isset($httpRequest->email) AND !empty($httpRequest->email)) { $condition['email'] = '%'.$httpRequest->email.'%'; }
        if (isset($httpRequest->phone) AND !empty($httpRequest->phone)) { $condition['phone'] = '%'.$httpRequest->phone.'%'; }
        if (isset($httpRequest->subjeck) AND !empty($httpRequest->subjeck)) { $condition['subjeck'] = '%'.$httpRequest->subjeck.'%'; }
        if (isset($httpRequest->message) AND !empty($httpRequest->message)) { $condition['message'] = '%'.$httpRequest->message.'%'; }
        
        if (count($condition) > 0) {
            $list = Inbox::select('*');
            foreach ($condition as $key => $value) { $list->where($key,'LIKE',$value); }
            $list = $list->get();
        }else{
            $list = Inbox::get();
        }

        return response()->json([
            'res' => true,
            'max_data' => count($list),
            'data' => $list,
        ]);
    }

    public function create(Request $httpRequest)
    {
        $cek = $this->validator($httpRequest->input());
        if ($cek != true) { return response()->json($cek); }

        $input = [];
        $input['name'] = $httpRequest->name;
        $input['email'] = $httpRequest->email;
        $input['phone'] = $httpRequest->phone;
        $input['subjeck'] = $httpRequest->subjeck;
        $input['message'] = $httpRequest->message;

        $store = Inbox::create($input);
        return response()->json([
            'res' => true,
            'message' => 'success',
            'data' => $store,
        ]);
    }

    public function update(Request $httpRequest, $id)
    {
        $cek = $this->validator($httpRequest->input());
        if ($cek != true) { return response()->json($cek); }

        $input = [];
        $input['name'] = $httpRequest->name;
        $input['email'] = $httpRequest->email;
        $input['phone'] = $httpRequest->phone;
        $input['subjeck'] = $httpRequest->subjeck;
        $input['message'] = $httpRequest->message;

        $store = Inbox::where('id',$id)->update($input);

        return response()->json([
            'res' => true,
            'message' => 'success',
            'data' => Inbox::find($id),
        ]);
    }

    public function delete($id)
    {
        Inbox::where('id',$id)->delete();
        return response()->json([
            'res' => true,
            'message' => 'success',
        ]);
    }

    public function find($id)
    {
        $data = Inbox::where('id',$id)->get();
        if (count($data) > 0) {
            return response()->json([
                'res' => true,
                'data' => $data[0],
            ]);
        }else{
            return response()->json([
                'res' => true,
                'message' => 'not found!',
            ]);
        }
    }
}

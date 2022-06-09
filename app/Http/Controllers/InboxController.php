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

    public function index()
    {
        $config = [
            'table' => [
                [ 'label' => 'Date', 'field' => 'created_at', 'order' => true, 'form' => false, 'search' => true, 'data_type' => 'date' ],
                [ 'label' => 'Name', 'field' => 'name', 'order' => true, 'form' => true, 'search' => true, 'data_type' => 'text' ],
                [ 'label' => 'Email', 'field' => 'email', 'order' => true, 'form' => true, 'search' => true, 'data_type' => 'text' ],
                [ 'label' => 'Subject', 'field' => 'subject', 'order' => true, 'form' => true, 'search' => true, 'data_type' => 'text' ],
                [ 'label' => 'Phone', 'field' => 'phone', 'order' => true, 'form' => true, 'search' => true, 'data_type' => 'text' ],
                [ 'label' => 'Tools', 'field' => 'tools', 'order' => false, 'form' => false, 'search' => false ],
            ],
            'endpoint' => [
                'list' => ['url'=>route('inbox-list'), 'method' => 'GET'],
                'store' => ['url'=>route('inbox-store'), 'method' => 'POST'],
                'update' => ['url'=>route('inbox-update'), 'method' => 'PUT'],
                'open' => ['url'=>route('inbox-open'), 'method' => 'GET'],
                'delete' => ['url'=>route('inbox-delete'), 'method' => 'DELETE'],
            ]
        ];
        
        return view('inbox', compact( 'config' ));
    }

    public function list(Request $httpRequest)
    {
        $condition = [];
        if (isset($httpRequest->name) AND !empty($httpRequest->name)) { $condition['name'] = '%'.$httpRequest->name.'%'; }
        if (isset($httpRequest->email) AND !empty($httpRequest->email)) { $condition['email'] = '%'.$httpRequest->email.'%'; }
        if (isset($httpRequest->phone) AND !empty($httpRequest->phone)) { $condition['phone'] = '%'.$httpRequest->phone.'%'; }
        if (isset($httpRequest->subjeck) AND !empty($httpRequest->subjeck)) { $condition['subjeck'] = '%'.$httpRequest->subjeck.'%'; }
        if (isset($httpRequest->message) AND !empty($httpRequest->message)) { $condition['message'] = '%'.$httpRequest->message.'%'; }
        if (isset($httpRequest->created_at) AND !empty($httpRequest->created_at)) { $condition['created_at'] = $httpRequest->created_at; }

        $show = 10;
        if (isset($httpRequest->show) AND !empty($httpRequest->show)) { $show = $httpRequest->show; }
        $orderBy = 'created_at';
        $orderByValue = 'DESC';
        if (isset($httpRequest->orderBy) AND !empty($httpRequest->orderBy)) { $orderBy = $httpRequest->orderBy; }
        if (isset($httpRequest->orderByValue) AND !empty($httpRequest->orderByValue)) { $orderByValue = $httpRequest->orderByValue; }
        
        if (count($condition) > 0) {
            $list = Inbox::select('*');
            foreach ($condition as $key => $value) {
                if ($key == 'created_at') {
                    $list->whereDate($key,$value);
                }else{ $list->where($key,'LIKE',$value); }
            }
            $list = $list->orderBy($orderBy,$orderByValue)->paginate($show);
        }else{
            $list = Inbox::orderBy($orderBy,$orderByValue)->paginate($show);
        }

        return response()->json([
            'res' => true,
            'condition' => $httpRequest->all(),
            'datas' => $list
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

    public function update(Request $httpRequest)
    {
        if (!isset($httpRequest->id) or empty($httpRequest->id)) {
            return response()->json(['res' => false, 'msg' => 'not found id']);
        }
        $cek = $this->validator($httpRequest->input());
        if ($cek != true) { return response()->json($cek); }

        $input = [];
        $input['name'] = $httpRequest->name;
        $input['email'] = $httpRequest->email;
        $input['phone'] = $httpRequest->phone;
        $input['subjeck'] = $httpRequest->subjeck;
        $input['message'] = $httpRequest->message;

        $store = Inbox::where('id',$httpRequest->id)->update($input);

        return response()->json([
            'res' => true,
            'message' => 'success',
            'data' => Inbox::find($id),
        ]);
    }

    public function delete(Request $httpRequest)
    {
        if (!isset($httpRequest->id) or empty($httpRequest->id)) {
            return response()->json(['res' => false, 'msg' => 'not found id']);
        }
        Inbox::where('id',$httpRequest->id)->delete();
        return response()->json([
            'res' => true,
            'message' => 'success',
        ]);
    }

    public function find(Request $httpRequest)
    {
        $data = Inbox::where('id',$httpRequest->id)->get();
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

<?php

namespace App\Http\Controllers;

use App\Task;
use Illuminate\Http\Request;

class TaskController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index()
    {
        return Task::all();
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title'=> 'required|string',
            'description'=> 'required|string',
        ]);

        $task = Task::create([
            'title'=> $request->title,
            'description'=> $request->description,
        ]);

        return response()->json(['message'=>'Task created successfully', 'task' => $task], 200);
    }

    public function show($task)
    {
        return Task::find($task);
    }

    public function update(Request $request, $task)
    {
        $this->validate($request, [
            'title'=> 'required|string',
            'description'=> 'required|string',
        ]);

        $task = Task::find($task);

        if($task->update([
            'title'=> $request->title,
            'description'=> $request->description,
        ]))
            return response()->json(["message"=>$task]);

        return response()->json(["message"=>"error"]);
    }

    public function destroy($task)
    {
        if (Task::find($task)->delete())
            return response()->json(["message"=>"Task successfully deleted"]);

        return response()->json(["message"=>"error"]);
    }
}

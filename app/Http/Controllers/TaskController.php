<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
       $notcompletedTasks = Task::orderBy('created_at', 'DESC')->get();
        return $notcompletedTasks;
    }

    public function completed()
    {
        $completedTasks = Task::where('completed', true)
            ->orderBy('created_at', 'DESC')
            ->get();

        return $completedTasks;
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        $newTask = new Task([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'category' => $request->input('category'),
            'person' => $request->input('person'),
            'position' => $request->input('position'),
        ]);
        $newTask->save();
        return response()->json($newTask, 201);
    }
  
    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
         $task = Task::find($id);

            if (!$task) {
                return response()->json(['message' => 'Task not found'], 404);
            }

            return response()->json(['task' => $task]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
       
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
         $existingTask = Task::find($id);

        if($existingTask) {
            $existingTask->update($request->all());
            return $existingTask;
        }

        return response()->json(['message' => 'Task not found'], 404);
    }

     public function updatecompleted(Request $request, string $id)
    {
         $existingTask = Task::find($id);

        if($existingTask) {
            $existingTask->completed = true;
            $existingTask->completed_at = now(); 
            $existingTask->update($request->all());
            return $existingTask;
        }

        return response()->json(['message' => 'Task not found'], 404);
    }

     public function incompleted(Request $request, string $id)
    {
         $existingTask = Task::find($id);

        if($existingTask) {
            $existingTask->completed = false;
            $existingTask->completed_at = now(); 
            $existingTask->update($request->all());
            return $existingTask;
        }

        return response()->json(['message' => 'Task not found'], 404);
    }



    
    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $existingTask = Task::find($id);

        if($existingTask) {
            $existingTask->delete();
            return "deleted successfully";
        }

         return response()->json(['message' => 'Task not found'], 404);
    }


    public function search(){

        $searchQuery = request('query');

        $tasks = Task::where('name', 'LIKE',"%{$searchQuery}%")->orderBy('created_at', 'DESC')->get();

        return response()->json($tasks);
    }

}
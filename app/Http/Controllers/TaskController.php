<?php
namespace App\Http\Controllers;

use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TaskController extends Controller
{
    public function index()
    {
        return response()->json(Task::where('user_id', Auth::id())->get(), 200);
    }

    public function store(Request $request)
    {
        $this->validate($request, [
            'title'       => 'required|string|max:150',
            'description' => 'nullable|string',
            'status'      => 'in:pending,done',
        ]);

        $task = Task::create([
            'user_id'     => Auth::id(),
            'title'       => $request->title,
            'description' => $request->description,
            'status'      => $request->status ?? 'pending',
        ]);

        return response()->json($task, 201);
    }

    public function show($id)
    {
        $task = Task::where('user_id', Auth::id())->find($id);
        if (!$task) return response()->json(['message' => 'Not Found'], 404);
        return response()->json($task, 200);
    }

    public function update(Request $request, $id)
    {
        $task = Task::where('user_id', Auth::id())->find($id);
        if (!$task) return response()->json(['message' => 'Not Found'], 404);

        $this->validate($request, [
            'title'       => 'sometimes|string|max:150',
            'description' => 'nullable|string',
            'status'      => 'sometimes|in:pending,done',
        ]);

        $task->update($request->only(['title', 'description', 'status']));
        return response()->json($task, 200);
    }

    public function destroy($id)
    {
        $task = Task::where('user_id', Auth::id())->find($id);
        if (!$task) return response()->json(['message' => 'Not Found'], 404);

        $task->delete();
        return response()->json(['message' => 'Deleted'], 200);
    }
}
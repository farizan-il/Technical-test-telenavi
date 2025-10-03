<?php

namespace App\Http\Controllers;

use App\Models\Todo;
use Illuminate\Http\Request;
use App\Exports\TodosExport; // Tambahkan ini
use Maatwebsite\Excel\Facades\Excel; // Tambahkan ini
use App\Http\Requests\StoreTodoRequest; // Tambahkan ini

class TodoController extends Controller
{
    // ==========================================================
    // 1. API Create Todo List (POST /api/todos)
    // ==========================================================
    public function store(StoreTodoRequest $request)
    {
        // Data sudah divalidasi oleh StoreTodoRequest
        $validated = $request->validated();

        // Terapkan default value untuk 'status' jika tidak disediakan
        // Persyaratan: status defaults to 'pending'
        $validated['status'] = $validated['status'] ?? 'pending';
        
        // Terapkan default value untuk 'time_tracked' jika tidak disediakan
        $validated['time_tracked'] = $validated['time_tracked'] ?? 0;

        // Simpan data ke database
        $todo = Todo::create($validated);

        return response()->json([
            'message' => 'Todo list created successfully',
            'data' => $todo
        ], 201); // 201 Created status
    }

    // ==========================================================
    // 2. API Get Todo List to Generate Excel Report (GET /api/todos/report/excel)
    // ==========================================================
    public function exportExcel(Request $request)
    {
        // 1. Ambil data dengan filter dari Request
        $todosQuery = Todo::query();

        // Filtering Logic
        // a. title (Partial match)
        if ($request->has('title')) {
            $todosQuery->where('title', 'like', '%' . $request->title . '%');
        }

        // b. assignee (Multiple strings separated by commas)
        if ($request->has('assignee')) {
            $assignees = explode(',', $request->assignee);
            $todosQuery->whereIn('assignee', $assignees);
        }
        
        // c. status (Multiple strings separated by commas)
        if ($request->has('status')) {
            $statuses = explode(',', $request->status);
            $todosQuery->whereIn('status', $statuses);
        }

        // d. priority (Multiple strings separated by commas)
        if ($request->has('priority')) {
            $priorities = explode(',', $request->priority);
            $todosQuery->whereIn('priority', $priorities);
        }

        // e. due_date (Range: start & end)
        if ($request->has('due_date_start') && $request->has('due_date_end')) {
            $todosQuery->whereBetween('due_date', [
                $request->due_date_start, 
                $request->due_date_end
            ]);
        }

        // f. time_tracked (Range: min & max)
        if ($request->has('time_tracked_min') && $request->has('time_tracked_max')) {
            $todosQuery->whereBetween('time_tracked', [
                $request->time_tracked_min, 
                $request->time_tracked_max
            ]);
        }
        
        // Ambil hasil query (Collection)
        $todos = $todosQuery->get();

        // 2. Generate Excel menggunakan Maatwebsite/Laravel-Excel
        // Pastikan Anda telah menginstal Maatwebsite/Excel
        return Excel::download(new TodosExport($todos), 'todo_report_' . time() . '.xlsx');
    }

    // ==========================================================
    // 3. API Get Todo List to Provide Chart Data (GET /api/chart?type=...)
    // ==========================================================
    public function chartData(Request $request)
    {
        $type = $request->query('type');
        $result = [];

        switch ($type) {
            case 'status':
                // Group by status and count the number of todos
                $result['status_summary'] = Todo::selectRaw('status, count(*) as count')
                    ->groupBy('status')
                    ->pluck('count', 'status');
                break;

            case 'priority':
                // Group by priority and count the number of todos
                $result['priority_summary'] = Todo::selectRaw('priority, count(*) as count')
                    ->groupBy('priority')
                    ->pluck('count', 'priority');
                break;

            case 'assignee':
                // Complex aggregation for Assignee Summary
                $assigneeData = Todo::select('assignee')
                    ->selectRaw('count(*) as total_todos')
                    ->selectRaw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as total_pending_todos')
                    ->selectRaw('SUM(CASE WHEN status = "completed" THEN time_tracked ELSE 0 END) as total_timetracked_completed_todos')
                    ->whereNotNull('assignee')
                    ->groupBy('assignee')
                    ->get();
                
                // Format the result into the required JSON structure
                $summary = $assigneeData->mapWithKeys(function ($item) {
                    return [
                        $item->assignee => [
                            'total_todos' => (int) $item->total_todos,
                            'total_pending_todos' => (int) $item->total_pending_todos,
                            'total_timetracked_completed_todos' => (int) $item->total_timetracked_completed_todos,
                        ]
                    ];
                });

                $result['assignee_summary'] = $summary;
                break;

            default:
                return response()->json([
                    'message' => 'Invalid chart type. Available types: status, priority, assignee'
                ], 400); // Bad Request
        }

        return response()->json($result);
    }
}
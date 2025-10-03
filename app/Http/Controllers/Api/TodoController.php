<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\StreamedResponse;

class TodoController extends Controller
{
    // API 1: Create Todo
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => 'required|string',
            'assignee' => 'nullable|string',
            'due_date' => 'required|date|after_or_equal:today',
            'time_tracked' => 'nullable|numeric|min:0',
            'status' => 'nullable|in:pending,open,in_progress,completed',
            'priority' => 'required|in:low,medium,high'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $todo = Todo::create([
            'title' => $request->title,
            'assignee' => $request->assignee,
            'due_date' => $request->due_date,
            'time_tracked' => $request->time_tracked ?? 0,
            'status' => $request->status ?? 'pending',
            'priority' => $request->priority
        ]);

        return response()->json([
            'message' => 'Todo created successfully',
            'data' => $todo
        ], 201);
    }

    // API 2: Get Todo List with Excel Export
    public function index(Request $request)
    {
        $query = Todo::query();

        // Filtering
        if ($request->has('title')) {
            $query->where('title', 'like', '%' . $request->title . '%');
        }

        if ($request->has('assignee')) {
            $assignees = explode(',', $request->assignee);
            $query->whereIn('assignee', $assignees);
        }

        if ($request->has('start') && $request->has('end')) {
            $query->whereBetween('due_date', [$request->start, $request->end]);
        }

        if ($request->has('min') && $request->has('max')) {
            $query->whereBetween('time_tracked', [$request->min, $request->max]);
        }

        if ($request->has('status')) {
            $statuses = explode(',', $request->status);
            $query->whereIn('status', $statuses);
        }

        if ($request->has('priority')) {
            $priorities = explode(',', $request->priority);
            $query->whereIn('priority', $priorities);
        }

        $todos = $query->get();

        // Generate Excel if requested
        if ($request->has('export') && $request->export == 'excel') {
            return $this->exportToExcel($todos);
        }

        return response()->json(['data' => $todos]);
    }

    private function exportToExcel($todos)
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Header styling (optional)
            $sheet->setCellValue('A1', 'Title');
            $sheet->setCellValue('B1', 'Assignee');
            $sheet->setCellValue('C1', 'Due Date');
            $sheet->setCellValue('D1', 'Time Tracked');
            $sheet->setCellValue('E1', 'Status');
            $sheet->setCellValue('F1', 'Priority');

            // Data
            $row = 2;
            foreach ($todos as $todo) {
                $sheet->setCellValue('A' . $row, $todo->title);
                $sheet->setCellValue('B' . $row, $todo->assignee ?? '-');
                $sheet->setCellValue('C' . $row, $todo->due_date->format('Y-m-d'));
                $sheet->setCellValue('D' . $row, $todo->time_tracked);
                $sheet->setCellValue('E' . $row, $todo->status);
                $sheet->setCellValue('F' . $row, $todo->priority);
                $row++;
            }

            // Summary Row
            $sheet->setCellValue('A' . $row, 'Total Todos: ' . $todos->count());
            $sheet->setCellValue('D' . $row, $todos->sum('time_tracked'));

            // Buat filename
            $fileName = 'todos_' . date('Y-m-d_His') . '.xlsx';

            // METODE 1: Direct Download (Recommended)
            $writer = new Xlsx($spreadsheet);
            
            // Return sebagai StreamedResponse
            return response()->stream(
                function() use ($writer) {
                    $writer->save('php://output');
                },
                200,
                [
                    'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                    'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                    'Cache-Control' => 'max-age=0',
                ]
            );

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to generate Excel file',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // ALTERNATIF: Jika metode di atas tidak work, gunakan ini
    private function exportToExcelAlternative($todos)
    {
        try {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            // Header
            $sheet->setCellValue('A1', 'Title');
            $sheet->setCellValue('B1', 'Assignee');
            $sheet->setCellValue('C1', 'Due Date');
            $sheet->setCellValue('D1', 'Time Tracked');
            $sheet->setCellValue('E1', 'Status');
            $sheet->setCellValue('F1', 'Priority');

            // Data
            $row = 2;
            foreach ($todos as $todo) {
                $sheet->setCellValue('A' . $row, $todo->title);
                $sheet->setCellValue('B' . $row, $todo->assignee ?? '-');
                $sheet->setCellValue('C' . $row, $todo->due_date->format('Y-m-d'));
                $sheet->setCellValue('D' . $row, $todo->time_tracked);
                $sheet->setCellValue('E' . $row, $todo->status);
                $sheet->setCellValue('F' . $row, $todo->priority);
                $row++;
            }

            // Summary Row
            $sheet->setCellValue('A' . $row, 'Total Todos: ' . $todos->count());
            $sheet->setCellValue('D' . $row, $todos->sum('time_tracked'));

            $writer = new Xlsx($spreadsheet);
            $fileName = 'todos_' . date('Y-m-d_His') . '.xlsx';
            
            // Simpan ke temp directory
            $filePath = storage_path('app/' . $fileName);
            $writer->save($filePath);

            // Check if file exists
            if (!file_exists($filePath)) {
                throw new \Exception('File could not be created');
            }

            // Download and delete
            return response()->download($filePath, $fileName, [
                'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])->deleteFileAfterSend(true);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to generate Excel file',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // API 3: Chart Data - Status Summary
    public function chartStatus()
    {
        $statusSummary = Todo::selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        return response()->json([
            'status_summary' => [
                'pending' => $statusSummary['pending'] ?? 0,
                'open' => $statusSummary['open'] ?? 0,
                'in_progress' => $statusSummary['in_progress'] ?? 0,
                'completed' => $statusSummary['completed'] ?? 0
            ]
        ]);
    }

    // API 3: Chart Data - Priority Summary
    public function chartPriority()
    {
        $prioritySummary = Todo::selectRaw('priority, COUNT(*) as count')
            ->groupBy('priority')
            ->pluck('count', 'priority');

        return response()->json([
            'priority_summary' => [
                'low' => $prioritySummary['low'] ?? 0,
                'medium' => $prioritySummary['medium'] ?? 0,
                'high' => $prioritySummary['high'] ?? 0
            ]
        ]);
    }

    // API 3: Chart Data - Assignee Summary
    public function chartAssignee()
    {
        $todos = Todo::whereNotNull('assignee')->get();
        $assigneeSummary = [];

        foreach ($todos as $todo) {
            if (!isset($assigneeSummary[$todo->assignee])) {
                $assigneeSummary[$todo->assignee] = [
                    'total_todos' => 0,
                    'total_pending_todos' => 0,
                    'total_timetracked_completed_todos' => 0
                ];
            }

            $assigneeSummary[$todo->assignee]['total_todos']++;
            
            if ($todo->status === 'pending') {
                $assigneeSummary[$todo->assignee]['total_pending_todos']++;
            }
            
            if ($todo->status === 'completed') {
                $assigneeSummary[$todo->assignee]['total_timetracked_completed_todos'] += $todo->time_tracked;
            }
        }

        return response()->json(['assignee_summary' => $assigneeSummary]);
    }
}
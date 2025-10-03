<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Todo;
use Illuminate\Http\UploadedFile;

class TodoTest extends TestCase
{
    use RefreshDatabase; // Menggunakan ini untuk mereset database setelah setiap tes

    /**
     * Uji coba API Create Todo List (POST).
     *
     * @return void
     */
    public function test_can_create_a_todo()
    {
        $data = [
            'title' => 'Kerjakan Technical Test',
            'assignee' => 'John Doe',
            'due_date' => now()->addDays(5)->toDateString(),
            'priority' => 'high'
        ];

        $response = $this->postJson('/api/todos', $data);

        $response->assertStatus(201)
                 ->assertJsonStructure([
                     'message',
                     'data' => [
                         'id',
                         'title',
                         'assignee',
                         'due_date',
                         'time_tracked',
                         'status',
                         'priority',
                         'created_at',
                         'updated_at'
                     ]
                 ]);

        // Verifikasi data di database
        $this->assertDatabaseHas('todos', [
            'title' => 'Kerjakan Technical Test',
            'status' => 'pending', // Verifikasi default value
        ]);
    }

    /**
     * Uji coba validasi due_date tidak di masa lalu.
     *
     * @return void
     */
    public function test_cannot_create_a_todo_with_past_due_date()
    {
        $data = [
            'title' => 'Invalid Todo',
            'due_date' => now()->subDay()->toDateString(), // Tanggal di masa lalu
            'priority' => 'medium'
        ];

        $response = $this->postJson('/api/todos', $data);

        $response->assertStatus(422) // 422 Unprocessable Entity untuk validasi
                 ->assertJsonValidationErrors(['due_date']);
    }

    /**
     * Uji coba API Get Todo List untuk Generate Excel Report.
     *
     * @return void
     */
    public function test_can_generate_excel_report_with_filters()
    {
        // Buat data dummy
        Todo::factory()->count(10)->create();
        Todo::factory()->create(['title' => 'Fix Bug A', 'status' => 'pending']);
        Todo::factory()->create(['title' => 'Fix Bug B', 'priority' => 'high']);

        $response = $this->get('/api/todos/report/excel?title=Fix%20Bug&status=pending&priority=high');
        
        $response->assertStatus(200)
                 ->assertHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    }

    /**
     * Uji coba API Get Todo List untuk Chart Data.
     *
     * @return void
     */
    public function test_can_get_chart_data_by_status()
    {
        Todo::factory()->count(5)->create(['status' => 'pending']);
        Todo::factory()->count(3)->create(['status' => 'completed']);
        
        $response = $this->getJson('/api/chart?type=status');

        $response->assertStatus(200)
                 ->assertJson([
                     'status_summary' => [
                         'pending' => 5,
                         'completed' => 3
                     ]
                 ]);
    }

    /**
     * Uji coba API Get Todo List untuk Chart Data by Assignee.
     *
     * @return void
     */
    public function test_can_get_chart_data_by_assignee()
    {
        // Buat data dummy
        Todo::factory()->create(['assignee' => 'John', 'status' => 'pending']);
        Todo::factory()->create(['assignee' => 'John', 'status' => 'completed', 'time_tracked' => 50]);
        Todo::factory()->create(['assignee' => 'Jane', 'status' => 'pending']);

        $response = $this->getJson('/api/chart?type=assignee');

        $response->assertStatus(200)
                 ->assertJson([
                     'assignee_summary' => [
                         'John' => [
                             'total_todos' => 2,
                             'total_pending_todos' => 1,
                             'total_timetracked_completed_todos' => 50,
                         ],
                         'Jane' => [
                             'total_todos' => 1,
                             'total_pending_todos' => 1,
                             'total_timetracked_completed_todos' => 0,
                         ],
                     ]
                 ]);
    }
}
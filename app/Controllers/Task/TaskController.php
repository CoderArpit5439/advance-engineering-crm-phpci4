<?php

namespace App\Controllers\Task;
helper("token");
use App\Models\Crm\TaskModel;
use CodeIgniter\RESTful\ResourceController;

class TaskController extends ResourceController
{
    public function addTask()
    {
        $TaskModel = new TaskModel();
        $isToken = check_jwt_authentication();

        if (!$isToken) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Authentication failed"
            ]);
        }

        $data = [
            "t_name" => $this->request->getVar("t_name") ?? "",
            "t_due_date" => $this->request->getVar("t_due_date") ?? "",
            "t_description" => $this->request->getVar("t_description") ?? "",
            "t_assign_to" => $this->request->getVar("t_assign_to") ?? "",
            "t_status" => $this->request->getVar("t_status") ?? "",

        ];

        try {
            $taskAdd = $TaskModel->insert($data);
            if ($taskAdd) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => "Task added successfully",
                    "data" => $data
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Task not added"
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => $th->getMessage()
            ]);
        }
    }
    public function updateTask()
    {
        $TaskModel = new TaskModel();
        $isToken = check_jwt_authentication();

        // Check for valid JWT token
        if (!$isToken) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Authentication failed"
            ]);
        }

        // Retrieve manufacturing ID from request
        $taskId = $this->request->getVar("t_id");

        if (!$taskId) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Inquiry ID is required"
            ]);
        }

        // Check if the manufacturing record exists
        $existingRecord = $TaskModel->find($taskId);

        if (!$existingRecord) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Task  not found"
            ]);
        }

        $data = [
            "t_name" => $this->request->getVar("t_name") ?? "",
            "t_due_date" => $this->request->getVar("t_due_date") ?? "",
            "t_description" => $this->request->getVar("t_description") ?? "",
            "t_assign_to" => $this->request->getVar("t_assign_to") ?? "",
            "t_status" => $this->request->getVar("t_status") ?? "",

        ];

        try {
            // Perform the update
            $updateSuccess = $TaskModel->update($taskId, $data);

            // Check if the update was successful
            if ($updateSuccess) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => "Task updated successfully",
                    "data" => $data
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Failed to update Task"
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => $th->getMessage()
            ]);
        }
    }
    public function removeTask()
    {
        $TaskModel = new TaskModel();
        $isToken = check_jwt_authentication();

        // Check for valid JWT token
        if (!$isToken) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Authentication failed"
            ]);
        }

        // Retrieve manufacturing ID from request
        $taskId = $this->request->getVar("t_id");

        if (!$taskId) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "inquiry ID is required"
            ]);
        }

        // Check if the manufacturing record exists
        $existingRecord = $TaskModel->find($taskId);

        if (!$existingRecord) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => "Task  not found"
            ]);
        }

        try {
            // Perform the delete
            $deleteSuccess = $TaskModel->delete($taskId);

            // Check if the delete was successful
            if ($deleteSuccess) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => "Task deleted successfully"
                ]);
            } else {
                return $this->response->setJSON([
                    "status" => "error",
                    "message" => "Failed to delete task"
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                "status" => "error",
                "message" => $th->getMessage()
            ]);
        }
    }
    public function fetchTask()
    {
        $TaskModel = new TaskModel();

        try {
            // Get the page number from the query string, default is 1
            $page = $this->request->getGet("page") ?? 1;
            $limit = 10; // Number of records per page
            $offset = ($page - 1) * $limit; // Calculate offset

            // Fetch manufacturing data with limit and offset for pagination
            $allTask = $TaskModel->orderBy("t_id", "DESC")
                ->findAll($limit, $offset); // Find records with limit and offset

            // Count the total number of records to calculate pagination
            $countTask = $TaskModel->countAllResults();

            // If records found, return data with pagination info
            if ($allTask) {
                return $this->response->setJSON([
                    "status" => "success",
                    "message" => count($allTask) . ' Task found',
                    "data" => $allTask,
                    "count" => $countTask // Total count for pagination purposes
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'data' => null,
                    'message' => 'No task found',
                ]);
            }
        } catch (\Throwable $th) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $th->getMessage(),
                'data' => null
            ]);
        }
    }
}

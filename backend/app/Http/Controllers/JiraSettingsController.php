<?php

namespace App\Http\Controllers;

use App\Models\JiraSettings;
use App\Services\JiraService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class JiraSettingsController extends Controller
{
    /**
     * Get the current Jira settings.
     */
    public function index(Request $request): JsonResponse
    {
        // For now, we'll use user_id = null (global settings)
        // Later, this can be extended to support per-user settings
        $userId = $request->input('user_id', null);

        $settings = JiraSettings::getActiveForUser($userId);

        if (!$settings) {
            return response()->json([
                'success' => true,
                'settings' => null,
                'message' => 'No Jira settings configured',
            ]);
        }

        return response()->json([
            'success' => true,
            'settings' => [
                'id' => $settings->id,
                'jira_domain' => $settings->jira_domain,
                'jira_email' => $settings->jira_email,
                'jira_project_key' => $settings->jira_project_key,
                'is_active' => $settings->is_active,
                'created_at' => $settings->created_at,
                'updated_at' => $settings->updated_at,
            ],
        ]);
    }

    /**
     * Store or update Jira settings.
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'nullable|exists:users,id',
            'jira_domain' => 'required|string|max:255',
            'jira_email' => 'required|email|max:255',
            'jira_api_token' => 'required|string',
            'jira_project_key' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $userId = $request->input('user_id', null);

        // Deactivate existing settings for this user
        JiraSettings::where('user_id', $userId)
            ->where('is_active', true)
            ->update(['is_active' => false]);

        // Create new settings
        $settings = JiraSettings::create([
            'user_id' => $userId,
            'jira_domain' => $request->input('jira_domain'),
            'jira_email' => $request->input('jira_email'),
            'jira_api_token' => $request->input('jira_api_token'),
            'jira_project_key' => $request->input('jira_project_key'),
            'is_active' => true,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Jira settings saved successfully',
            'settings' => [
                'id' => $settings->id,
                'jira_domain' => $settings->jira_domain,
                'jira_email' => $settings->jira_email,
                'jira_project_key' => $settings->jira_project_key,
                'is_active' => $settings->is_active,
            ],
        ], 201);
    }

    /**
     * Update existing Jira settings.
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $settings = JiraSettings::find($id);

        if (!$settings) {
            return response()->json([
                'success' => false,
                'message' => 'Jira settings not found',
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'jira_domain' => 'sometimes|required|string|max:255',
            'jira_email' => 'sometimes|required|email|max:255',
            'jira_api_token' => 'sometimes|required|string',
            'jira_project_key' => 'nullable|string|max:50',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $settings->update($request->only([
            'jira_domain',
            'jira_email',
            'jira_api_token',
            'jira_project_key',
        ]));

        return response()->json([
            'success' => true,
            'message' => 'Jira settings updated successfully',
            'settings' => [
                'id' => $settings->id,
                'jira_domain' => $settings->jira_domain,
                'jira_email' => $settings->jira_email,
                'jira_project_key' => $settings->jira_project_key,
                'is_active' => $settings->is_active,
            ],
        ]);
    }

    /**
     * Delete Jira settings.
     */
    public function destroy(int $id): JsonResponse
    {
        $settings = JiraSettings::find($id);

        if (!$settings) {
            return response()->json([
                'success' => false,
                'message' => 'Jira settings not found',
            ], 404);
        }

        $settings->delete();

        return response()->json([
            'success' => true,
            'message' => 'Jira settings deleted successfully',
        ]);
    }

    /**
     * Test the Jira connection.
     */
    public function testConnection(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'jira_domain' => 'required|string',
            'jira_email' => 'required|email',
            'jira_api_token' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        // Create temporary settings for testing
        $tempSettings = new JiraSettings([
            'jira_domain' => $request->input('jira_domain'),
            'jira_email' => $request->input('jira_email'),
            'jira_api_token' => $request->input('jira_api_token'),
        ]);

        $jiraService = new JiraService($tempSettings);
        $result = $jiraService->testConnection();

        return response()->json($result);
    }

    /**
     * Get Jira projects.
     */
    public function getProjects(Request $request): JsonResponse
    {
        $userId = $request->input('user_id', null);
        $settings = JiraSettings::getActiveForUser($userId);

        if (!$settings) {
            return response()->json([
                'success' => false,
                'message' => 'No Jira settings configured',
            ], 404);
        }

        $jiraService = new JiraService($settings);
        $result = $jiraService->getProjects();

        return response()->json($result);
    }
}

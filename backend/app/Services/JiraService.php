<?php

namespace App\Services;

use App\Models\JiraSettings;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class JiraService
{
    private ?JiraSettings $settings;
    private string $baseUrl;

    public function __construct(?JiraSettings $settings = null)
    {
        $this->settings = $settings;
        if ($settings) {
            $this->baseUrl = "https://{$settings->jira_domain}/rest/api/3";
        }
    }

    /**
     * Set Jira settings for this service instance.
     */
    public function setSettings(JiraSettings $settings): self
    {
        $this->settings = $settings;
        $this->baseUrl = "https://{$settings->jira_domain}/rest/api/3";
        return $this;
    }

    /**
     * Get the authenticated HTTP client.
     */
    private function getClient()
    {
        if (!$this->settings) {
            throw new \Exception('Jira settings not configured');
        }

        return Http::withBasicAuth(
            $this->settings->jira_email,
            $this->settings->jira_api_token
        )
            ->acceptJson()
            ->timeout(30);
    }

    /**
     * Test the Jira connection and credentials.
     */
    public function testConnection(): array
    {
        try {
            $response = $this->getClient()->get("{$this->baseUrl}/myself");

            if ($response->successful()) {
                $user = $response->json();
                return [
                    'success' => true,
                    'message' => 'Connection successful',
                    'user' => [
                        'displayName' => $user['displayName'] ?? null,
                        'emailAddress' => $user['emailAddress'] ?? null,
                        'accountId' => $user['accountId'] ?? null,
                    ],
                ];
            }

            return [
                'success' => false,
                'message' => 'Connection failed: ' . $response->body(),
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('Jira connection test failed', [
                'error' => $e->getMessage(),
                'domain' => $this->settings->jira_domain ?? null,
            ]);

            return [
                'success' => false,
                'message' => 'Connection error: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Get all projects.
     */
    public function getProjects(): array
    {
        try {
            $response = $this->getClient()->get("{$this->baseUrl}/project");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'projects' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to fetch projects',
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to fetch Jira projects', ['error' => $e->getMessage()]);
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get issues from a project.
     */
    public function getIssues(string $projectKey, int $maxResults = 50, int $startAt = 0): array
    {
        try {
            $jql = "project = {$projectKey} ORDER BY created DESC";

            $response = $this->getClient()->get("{$this->baseUrl}/search", [
                'jql' => $jql,
                'maxResults' => $maxResults,
                'startAt' => $startAt,
                'fields' => ['summary', 'status', 'assignee', 'created', 'updated', 'priority', 'issuetype'],
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'issues' => $response->json('issues'),
                    'total' => $response->json('total'),
                    'maxResults' => $response->json('maxResults'),
                    'startAt' => $response->json('startAt'),
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to fetch issues',
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to fetch Jira issues', [
                'error' => $e->getMessage(),
                'projectKey' => $projectKey,
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get a single issue by key.
     */
    public function getIssue(string $issueKey): array
    {
        try {
            $response = $this->getClient()->get("{$this->baseUrl}/issue/{$issueKey}");

            if ($response->successful()) {
                return [
                    'success' => true,
                    'issue' => $response->json(),
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to fetch issue',
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to fetch Jira issue', [
                'error' => $e->getMessage(),
                'issueKey' => $issueKey,
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Create a new issue.
     */
    public function createIssue(string $projectKey, string $summary, string $description, string $issueType = 'Task'): array
    {
        try {
            $response = $this->getClient()->post("{$this->baseUrl}/issue", [
                'fields' => [
                    'project' => [
                        'key' => $projectKey,
                    ],
                    'summary' => $summary,
                    'description' => [
                        'type' => 'doc',
                        'version' => 1,
                        'content' => [
                            [
                                'type' => 'paragraph',
                                'content' => [
                                    [
                                        'type' => 'text',
                                        'text' => $description,
                                    ],
                                ],
                            ],
                        ],
                    ],
                    'issuetype' => [
                        'name' => $issueType,
                    ],
                ],
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'issue' => $response->json(),
                    'message' => 'Issue created successfully',
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to create issue',
                'status' => $response->status(),
                'body' => $response->json(),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to create Jira issue', [
                'error' => $e->getMessage(),
                'projectKey' => $projectKey,
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Add a comment to an issue.
     */
    public function addComment(string $issueKey, string $comment): array
    {
        try {
            $response = $this->getClient()->post("{$this->baseUrl}/issue/{$issueKey}/comment", [
                'body' => [
                    'type' => 'doc',
                    'version' => 1,
                    'content' => [
                        [
                            'type' => 'paragraph',
                            'content' => [
                                [
                                    'type' => 'text',
                                    'text' => $comment,
                                ],
                            ],
                        ],
                    ],
                ],
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'comment' => $response->json(),
                    'message' => 'Comment added successfully',
                ];
            }

            return [
                'success' => false,
                'message' => 'Failed to add comment',
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to add comment to Jira issue', [
                'error' => $e->getMessage(),
                'issueKey' => $issueKey,
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Search issues by JQL.
     */
    public function searchIssues(string $jql, int $maxResults = 50, int $startAt = 0): array
    {
        try {
            $response = $this->getClient()->get("{$this->baseUrl}/search", [
                'jql' => $jql,
                'maxResults' => $maxResults,
                'startAt' => $startAt,
            ]);

            if ($response->successful()) {
                return [
                    'success' => true,
                    'issues' => $response->json('issues'),
                    'total' => $response->json('total'),
                ];
            }

            return [
                'success' => false,
                'message' => 'Search failed',
                'status' => $response->status(),
            ];
        } catch (\Exception $e) {
            Log::error('Failed to search Jira issues', [
                'error' => $e->getMessage(),
                'jql' => $jql,
            ]);

            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}

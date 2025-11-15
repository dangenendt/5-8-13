"use client";

import { useEffect, useState } from "react";

interface JiraSettings {
	id?: number;
	jira_domain: string;
	jira_email: string;
	jira_project_key: string;
	is_active?: boolean;
}

interface TestConnectionResult {
	success: boolean;
	message: string;
	user?: {
		displayName: string;
		emailAddress: string;
		accountId: string;
	};
}

export default function JiraSettingsPage() {
	const [settings, setSettings] = useState<JiraSettings>({
		jira_domain: "",
		jira_email: "",
		jira_project_key: "",
	});
	const [apiToken, setApiToken] = useState("");
	const [isLoading, setIsLoading] = useState(false);
	const [isTesting, setIsTesting] = useState(false);
	const [message, setMessage] = useState<{
		type: "success" | "error" | "info";
		text: string;
	} | null>(null);
	const [testResult, setTestResult] = useState<TestConnectionResult | null>(
		null,
	);
	const [hasExistingSettings, setHasExistingSettings] = useState(false);

	useEffect(() => {
		loadSettings();
	}, []);

	const loadSettings = async () => {
		try {
			const response = await fetch("http://localhost:8000/api/jira/settings");
			const data = await response.json();

			if (data.success && data.settings) {
				setSettings(data.settings);
				setHasExistingSettings(true);
			}
		} catch (error) {
			console.error("Error loading settings:", error);
		}
	};

	const handleTestConnection = async () => {
		if (!settings.jira_domain || !settings.jira_email || !apiToken) {
			setMessage({
				type: "error",
				text: "Please fill in domain, email, and API token",
			});
			return;
		}

		setIsTesting(true);
		setTestResult(null);
		setMessage(null);

		try {
			const response = await fetch(
				"http://localhost:8000/api/jira/test-connection",
				{
					method: "POST",
					headers: {
						"Content-Type": "application/json",
						Accept: "application/json",
					},
					body: JSON.stringify({
						jira_domain: settings.jira_domain,
						jira_email: settings.jira_email,
						jira_api_token: apiToken,
					}),
				},
			);

			const data = await response.json();
			setTestResult(data);

			if (data.success) {
				setMessage({
					type: "success",
					text: `Connection successful! Authenticated as ${data.user?.displayName}`,
				});
			} else {
				setMessage({
					type: "error",
					text: `Connection failed: ${data.message}`,
				});
			}
		} catch (error) {
			setMessage({
				type: "error",
				text: `Error testing connection: ${error}`,
			});
		} finally {
			setIsTesting(false);
		}
	};

	const handleSaveSettings = async () => {
		if (!settings.jira_domain || !settings.jira_email || !apiToken) {
			setMessage({
				type: "error",
				text: "Please fill in all required fields",
			});
			return;
		}

		setIsLoading(true);
		setMessage(null);

		try {
			const url = hasExistingSettings && settings.id
				? `http://localhost:8000/api/jira/settings/${settings.id}`
				: "http://localhost:8000/api/jira/settings";

			const method = hasExistingSettings && settings.id ? "PUT" : "POST";

			const response = await fetch(url, {
				method,
				headers: {
					"Content-Type": "application/json",
					Accept: "application/json",
				},
				body: JSON.stringify({
					jira_domain: settings.jira_domain,
					jira_email: settings.jira_email,
					jira_api_token: apiToken,
					jira_project_key: settings.jira_project_key || null,
				}),
			});

			const data = await response.json();

			if (data.success) {
				setMessage({
					type: "success",
					text: "Settings saved successfully!",
				});
				setSettings(data.settings);
				setHasExistingSettings(true);
				setApiToken(""); // Clear API token after saving
			} else {
				setMessage({
					type: "error",
					text: `Failed to save settings: ${data.message}`,
				});
			}
		} catch (error) {
			setMessage({
				type: "error",
				text: `Error saving settings: ${error}`,
			});
		} finally {
			setIsLoading(false);
		}
	};

	const handleDeleteSettings = async () => {
		if (!settings.id) return;

		if (!confirm("Are you sure you want to delete these Jira settings?")) {
			return;
		}

		setIsLoading(true);
		setMessage(null);

		try {
			const response = await fetch(
				`http://localhost:8000/api/jira/settings/${settings.id}`,
				{
					method: "DELETE",
					headers: {
						Accept: "application/json",
					},
				},
			);

			const data = await response.json();

			if (data.success) {
				setMessage({
					type: "success",
					text: "Settings deleted successfully!",
				});
				setSettings({
					jira_domain: "",
					jira_email: "",
					jira_project_key: "",
				});
				setApiToken("");
				setHasExistingSettings(false);
				setTestResult(null);
			} else {
				setMessage({
					type: "error",
					text: `Failed to delete settings: ${data.message}`,
				});
			}
		} catch (error) {
			setMessage({
				type: "error",
				text: `Error deleting settings: ${error}`,
			});
		} finally {
			setIsLoading(false);
		}
	};

	return (
		<div className="min-h-screen bg-gray-950 text-white p-8">
			<div className="max-w-3xl mx-auto">
				<h1 className="text-4xl font-bold mb-2">Jira Integration Settings</h1>
				<p className="text-gray-400 mb-8">
					Configure your Jira credentials to enable integration
				</p>

				{/* Message Banner */}
				{message && (
					<div
						className={`mb-6 p-4 rounded-lg ${
							message.type === "success"
								? "bg-green-900/50 border border-green-700 text-green-200"
								: message.type === "error"
									? "bg-red-900/50 border border-red-700 text-red-200"
									: "bg-blue-900/50 border border-blue-700 text-blue-200"
						}`}
					>
						{message.text}
					</div>
				)}

				{/* Form */}
				<div className="bg-gray-900 rounded-lg p-6 mb-6">
					<div className="space-y-6">
						{/* Jira Domain */}
						<div>
							<label
								htmlFor="jira_domain"
								className="block text-sm font-medium mb-2"
							>
								Jira Domain <span className="text-red-500">*</span>
							</label>
							<input
								type="text"
								id="jira_domain"
								value={settings.jira_domain}
								onChange={(e) =>
									setSettings({ ...settings, jira_domain: e.target.value })
								}
								placeholder="your-company.atlassian.net"
								className="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
							/>
							<p className="text-xs text-gray-500 mt-1">
								Enter your Jira domain without https://
							</p>
						</div>

						{/* Email */}
						<div>
							<label
								htmlFor="jira_email"
								className="block text-sm font-medium mb-2"
							>
								Email Address <span className="text-red-500">*</span>
							</label>
							<input
								type="email"
								id="jira_email"
								value={settings.jira_email}
								onChange={(e) =>
									setSettings({ ...settings, jira_email: e.target.value })
								}
								placeholder="your.email@company.com"
								className="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
							/>
							<p className="text-xs text-gray-500 mt-1">
								Your Jira account email address
							</p>
						</div>

						{/* API Token */}
						<div>
							<label
								htmlFor="api_token"
								className="block text-sm font-medium mb-2"
							>
								API Token <span className="text-red-500">*</span>
							</label>
							<input
								type="password"
								id="api_token"
								value={apiToken}
								onChange={(e) => setApiToken(e.target.value)}
								placeholder="Enter your Jira API token"
								className="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
							/>
							<p className="text-xs text-gray-500 mt-1">
								Create an API token at{" "}
								<a
									href="https://id.atlassian.com/manage-profile/security/api-tokens"
									target="_blank"
									rel="noopener noreferrer"
									className="text-blue-400 hover:text-blue-300"
								>
									Atlassian Account Settings
								</a>
							</p>
						</div>

						{/* Project Key (Optional) */}
						<div>
							<label
								htmlFor="project_key"
								className="block text-sm font-medium mb-2"
							>
								Default Project Key (Optional)
							</label>
							<input
								type="text"
								id="project_key"
								value={settings.jira_project_key}
								onChange={(e) =>
									setSettings({ ...settings, jira_project_key: e.target.value })
								}
								placeholder="PROJ"
								className="w-full px-4 py-2 bg-gray-800 border border-gray-700 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"
							/>
							<p className="text-xs text-gray-500 mt-1">
								Default project to use for creating issues
							</p>
						</div>
					</div>
				</div>

				{/* Test Connection Result */}
				{testResult?.success && testResult.user && (
					<div className="mb-6 p-4 bg-green-900/20 border border-green-700 rounded-lg">
						<h3 className="font-semibold text-green-400 mb-2">
							Connection Test Successful
						</h3>
						<div className="text-sm text-gray-300 space-y-1">
							<p>
								<span className="text-gray-400">Name:</span>{" "}
								{testResult.user.displayName}
							</p>
							<p>
								<span className="text-gray-400">Email:</span>{" "}
								{testResult.user.emailAddress}
							</p>
							<p>
								<span className="text-gray-400">Account ID:</span>{" "}
								{testResult.user.accountId}
							</p>
						</div>
					</div>
				)}

				{/* Action Buttons */}
				<div className="flex gap-4">
					<button
						type="button"
						onClick={handleTestConnection}
						disabled={isTesting}
						className="px-6 py-3 bg-purple-600 hover:bg-purple-700 disabled:bg-gray-700 disabled:cursor-not-allowed rounded-lg font-semibold transition-colors"
					>
						{isTesting ? "Testing..." : "Test Connection"}
					</button>

					<button
						type="button"
						onClick={handleSaveSettings}
						disabled={isLoading}
						className="px-6 py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-700 disabled:cursor-not-allowed rounded-lg font-semibold transition-colors"
					>
						{isLoading ? "Saving..." : "Save Settings"}
					</button>

					{hasExistingSettings && settings.id && (
						<button
							type="button"
							onClick={handleDeleteSettings}
							disabled={isLoading}
							className="px-6 py-3 bg-red-600 hover:bg-red-700 disabled:bg-gray-700 disabled:cursor-not-allowed rounded-lg font-semibold transition-colors"
						>
							Delete Settings
						</button>
					)}
				</div>

				{/* Info Section */}
				<div className="mt-8 p-4 bg-gray-900 rounded-lg">
					<h3 className="text-lg font-semibold mb-2">How to get your API Token:</h3>
					<ol className="list-decimal list-inside space-y-2 text-gray-300 text-sm">
						<li>
							Go to{" "}
							<a
								href="https://id.atlassian.com/manage-profile/security/api-tokens"
								target="_blank"
								rel="noopener noreferrer"
								className="text-blue-400 hover:text-blue-300"
							>
								Atlassian Account Settings
							</a>
						</li>
						<li>Click "Create API token"</li>
						<li>Give it a label (e.g., "My App Integration")</li>
						<li>Copy the token and paste it above</li>
						<li>Click "Test Connection" to verify your credentials</li>
						<li>Click "Save Settings" to store your configuration</li>
					</ol>
					<p className="mt-4 text-xs text-gray-500">
						Your API token is encrypted before being stored in the database.
					</p>
				</div>
			</div>
		</div>
	);
}

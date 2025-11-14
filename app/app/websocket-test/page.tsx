"use client";

import { useEffect, useState } from "react";
import { getEcho } from "@/lib/echo";

interface BroadcastMessage {
	message: string;
	timestamp: string;
}

export default function WebSocketTestPage() {
	const [messages, setMessages] = useState<string[]>([]);
	const [isConnected, setIsConnected] = useState(false);
	const [isSending, setIsSending] = useState(false);

	useEffect(() => {
		// Initialize Echo client
		const echo = getEcho();

		// Listen for connection state changes
		if (echo.connector.pusher) {
			echo.connector.pusher.connection.bind("connected", () => {
				console.log("WebSocket connected!");
				setIsConnected(true);
			});

			echo.connector.pusher.connection.bind("disconnected", () => {
				console.log("WebSocket disconnected!");
				setIsConnected(false);
			});

			echo.connector.pusher.connection.bind("error", (error: any) => {
				console.error("WebSocket error:", error);
			});
		}

		// Subscribe to the test channel
		const channel = echo.channel("test-channel");

		// Listen for the test event
		channel.listen(".test.event", (data: BroadcastMessage) => {
			console.log("Received test event:", data);
			const logMessage = `[${new Date().toLocaleTimeString()}] ${data.message}`;
			setMessages((prev) => [...prev, logMessage]);
		});

		// Cleanup on unmount
		return () => {
			channel.stopListening(".test.event");
			echo.leaveChannel("test-channel");
		};
	}, []);

	const sendTestBroadcast = async () => {
		setIsSending(true);
		try {
			const response = await fetch("http://localhost:8000/api/test/broadcast", {
				method: "POST",
				headers: {
					"Content-Type": "application/json",
					Accept: "application/json",
				},
			});

			if (!response.ok) {
				throw new Error(`HTTP error! status: ${response.status}`);
			}

			const result = await response.json();
			console.log("Broadcast triggered:", result);
		} catch (error) {
			console.error("Error triggering broadcast:", error);
			setMessages((prev) => [
				...prev,
				`[${new Date().toLocaleTimeString()}] Error: ${error}`,
			]);
		} finally {
			setIsSending(false);
		}
	};

	return (
		<div className="min-h-screen bg-gray-950 text-white p-8">
			<div className="max-w-4xl mx-auto">
				<h1 className="text-4xl font-bold mb-8">WebSocket Test</h1>

				{/* Connection Status */}
				<div className="mb-8 p-4 rounded-lg bg-gray-900">
					<div className="flex items-center gap-3">
						<div
							className={`w-3 h-3 rounded-full ${
								isConnected ? "bg-green-500" : "bg-red-500"
							}`}
						/>
						<span className="text-lg">
							Status:{" "}
							<span
								className={isConnected ? "text-green-400" : "text-red-400"}
							>
								{isConnected ? "Connected" : "Disconnected"}
							</span>
						</span>
					</div>
					<p className="text-gray-400 text-sm mt-2">
						Channel: <code className="text-blue-400">test-channel</code>
					</p>
					<p className="text-gray-400 text-sm">
						Event: <code className="text-blue-400">test.event</code>
					</p>
				</div>

				{/* Trigger Button */}
				<div className="mb-8">
					<button
						type="button"
						onClick={sendTestBroadcast}
						disabled={isSending}
						className="px-6 py-3 bg-blue-600 hover:bg-blue-700 disabled:bg-gray-700 disabled:cursor-not-allowed rounded-lg font-semibold transition-colors"
					>
						{isSending ? "Sending..." : "Send Test Broadcast"}
					</button>
					<p className="text-gray-400 text-sm mt-2">
						This will trigger a broadcast from the backend
					</p>
				</div>

				{/* Messages Log */}
				<div className="bg-gray-900 rounded-lg p-6">
					<h2 className="text-2xl font-semibold mb-4">Received Messages</h2>
					<div className="bg-black rounded p-4 font-mono text-sm max-h-96 overflow-y-auto">
						{messages.length === 0 ? (
							<p className="text-gray-500">
								No messages received yet. Click the button above to send a test
								broadcast.
							</p>
						) : (
							<ul className="space-y-1">
								{messages.map((msg, index) => (
									<li key={index} className="text-green-400">
										{msg}
									</li>
								))}
							</ul>
						)}
					</div>
					{messages.length > 0 && (
						<button
							type="button"
							onClick={() => setMessages([])}
							className="mt-4 px-4 py-2 bg-gray-800 hover:bg-gray-700 rounded text-sm transition-colors"
						>
							Clear Messages
						</button>
					)}
				</div>

				{/* Instructions */}
				<div className="mt-8 p-4 bg-gray-900 rounded-lg">
					<h3 className="text-lg font-semibold mb-2">How it works:</h3>
					<ol className="list-decimal list-inside space-y-2 text-gray-300">
						<li>The page connects to the WebSocket server via Laravel Echo</li>
						<li>
							It subscribes to the <code>test-channel</code> channel
						</li>
						<li>
							When you click the button, it sends a POST request to the backend
						</li>
						<li>
							The backend broadcasts a <code>test.event</code> event
						</li>
						<li>The frontend receives the event and logs it to the console</li>
						<li>The message is also displayed on this page</li>
					</ol>
					<p className="mt-4 text-sm text-gray-400">
						Open the browser console (F12) to see the console.log() output!
					</p>
				</div>
			</div>
		</div>
	);
}

"use client";

import { motion } from "framer-motion";
import { useState } from "react";
import { EmojiCanvas } from "@/components/EmojiCanvas";
import { useRealtimeEmojis } from "@/hooks/useRealtimeEmojis";

export default function DemoPage() {
	const [roomId, setRoomId] = useState("demo-room");
	const [userName, setUserName] = useState("Demo User");
	const { throwEmoji, isConnected } = useRealtimeEmojis(roomId);

	const quickEmojis = ["🎉", "❤️", "👏", "🔥", "⭐", "💯", "😂", "🚀"];

	const handleRandomEmoji = () => {
		throwEmoji(undefined, userName);
	};

	const handleQuickEmoji = (emoji: string) => {
		throwEmoji(emoji, userName);
	};

	return (
		<div className="relative min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-blue-50 dark:from-gray-900 dark:via-purple-900 dark:to-blue-900">
			{/* Emoji Canvas */}
			<EmojiCanvas roomId={roomId} enableWebSocket={true} />

			{/* Demo Controls */}
			<main className="flex min-h-screen flex-col items-center justify-center px-4 py-16">
				<div className="mx-auto w-full max-w-4xl">
					{/* Header */}
					<div className="mb-8 text-center">
						<h1 className="mb-4 bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-5xl font-bold text-transparent">
							WebSocket Emoji Demo
						</h1>
						<p className="text-lg text-gray-600 dark:text-gray-300">
							Teste den Echtzeit-Emoji-Werfer mit Laravel Reverb
						</p>
					</div>

					{/* Settings Card */}
					<motion.div
						initial={{ opacity: 0, y: 20 }}
						animate={{ opacity: 1, y: 0 }}
						className="mb-6 rounded-2xl bg-white/80 p-6 shadow-lg backdrop-blur-sm dark:bg-gray-800/80"
					>
						<h2 className="mb-4 text-xl font-semibold text-gray-900 dark:text-white">
							Einstellungen
						</h2>

						<div className="grid gap-4 md:grid-cols-2">
							{/* Username Input */}
							<div>
								<label
									htmlFor="userName"
									className="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
								>
									Dein Name
								</label>
								<input
									type="text"
									id="userName"
									value={userName}
									onChange={(e) => setUserName(e.target.value)}
									className="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
									placeholder="Dein Name"
								/>
							</div>

							{/* Room ID Input */}
							<div>
								<label
									htmlFor="roomId"
									className="mb-2 block text-sm font-medium text-gray-700 dark:text-gray-300"
								>
									Room ID
								</label>
								<input
									type="text"
									id="roomId"
									value={roomId}
									onChange={(e) => setRoomId(e.target.value)}
									className="w-full rounded-lg border border-gray-300 bg-white px-4 py-2 text-gray-900 focus:border-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white"
									placeholder="demo-room"
								/>
							</div>
						</div>

						{/* Connection Status */}
						<div className="mt-4 flex items-center gap-2">
							<div
								className={`h-3 w-3 rounded-full ${
									isConnected ? "bg-green-500" : "bg-red-500"
								}`}
							/>
							<span className="text-sm text-gray-600 dark:text-gray-300">
								{isConnected
									? `Verbunden mit Room: ${roomId}`
									: "Nicht verbunden"}
							</span>
						</div>
					</motion.div>

					{/* Quick Actions */}
					<motion.div
						initial={{ opacity: 0, y: 20 }}
						animate={{ opacity: 1, y: 0 }}
						transition={{ delay: 0.1 }}
						className="mb-6 rounded-2xl bg-white/80 p-6 shadow-lg backdrop-blur-sm dark:bg-gray-800/80"
					>
						<h2 className="mb-4 text-xl font-semibold text-gray-900 dark:text-white">
							Schnell-Aktionen
						</h2>

						<div className="grid gap-4 md:grid-cols-2">
							{/* Random Emoji Button */}
							<motion.button
								type="button"
								onClick={handleRandomEmoji}
								disabled={!isConnected}
								whileHover={{ scale: 1.02 }}
								whileTap={{ scale: 0.98 }}
								className="rounded-xl bg-gradient-to-r from-purple-600 to-pink-600 px-6 py-4 text-lg font-semibold text-white shadow-lg transition-all hover:shadow-xl disabled:cursor-not-allowed disabled:opacity-50"
							>
								🎲 Zufälliges Emoji werfen
							</motion.button>

							{/* Info Card */}
							<div className="rounded-xl border-2 border-purple-200 bg-purple-50 p-4 dark:border-purple-700 dark:bg-purple-900/20">
								<p className="text-sm text-purple-900 dark:text-purple-100">
									💡 <strong>Tipp:</strong> Öffne diese Seite in mehreren
									Browser-Tabs oder teile die URL mit anderen, um Emojis in
									Echtzeit zu teilen!
								</p>
							</div>
						</div>
					</motion.div>

					{/* Quick Emoji Buttons */}
					<motion.div
						initial={{ opacity: 0, y: 20 }}
						animate={{ opacity: 1, y: 0 }}
						transition={{ delay: 0.2 }}
						className="rounded-2xl bg-white/80 p-6 shadow-lg backdrop-blur-sm dark:bg-gray-800/80"
					>
						<h2 className="mb-4 text-xl font-semibold text-gray-900 dark:text-white">
							Quick Emojis
						</h2>

						<div className="grid grid-cols-4 gap-3 md:grid-cols-8">
							{quickEmojis.map((emoji) => (
								<motion.button
									type="button"
									key={emoji}
									onClick={() => handleQuickEmoji(emoji)}
									disabled={!isConnected}
									whileHover={{ scale: 1.1 }}
									whileTap={{ scale: 0.9 }}
									className="rounded-xl bg-gray-100 p-4 text-4xl transition-all hover:bg-gradient-to-br hover:from-purple-100 hover:to-pink-100 disabled:cursor-not-allowed disabled:opacity-50 dark:bg-gray-700 dark:hover:from-purple-900/50 dark:hover:to-pink-900/50"
								>
									{emoji}
								</motion.button>
							))}
						</div>
					</motion.div>

					{/* Instructions */}
					<motion.div
						initial={{ opacity: 0, y: 20 }}
						animate={{ opacity: 1, y: 0 }}
						transition={{ delay: 0.3 }}
						className="mt-6 rounded-2xl bg-blue-50 p-6 dark:bg-blue-900/20"
					>
						<h3 className="mb-3 text-lg font-semibold text-blue-900 dark:text-blue-100">
							📖 Anleitung
						</h3>
						<ul className="space-y-2 text-sm text-blue-800 dark:text-blue-200">
							<li>
								✅ Klicke auf einen der Emoji-Buttons, um ein Emoji zu werfen
							</li>
							<li>
								✅ Das Emoji wird über WebSocket an alle Teilnehmer im gleichen
								Room gesendet
							</li>
							<li>
								✅ Öffne diese Seite in mehreren Tabs mit der gleichen Room ID
							</li>
							<li>
								✅ Verwende den Emoji-Picker unten, um aus 16 verschiedenen
								Emojis zu wählen
							</li>
							<li>
								✅ Jedes Emoji wird mit einer schönen Animation über den
								Bildschirm fliegen
							</li>
						</ul>
					</motion.div>

					{/* Back to Home */}
					<div className="mt-6 text-center">
						<a
							href="/"
							className="inline-block text-purple-600 hover:text-purple-700 dark:text-purple-400 dark:hover:text-purple-300"
						>
							← Zurück zur Startseite
						</a>
					</div>
				</div>
			</main>
		</div>
	);
}

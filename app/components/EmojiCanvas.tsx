"use client";

import { useCallback, useEffect, useState } from "react";
import { useRealtimeEmojis } from "@/hooks/useRealtimeEmojis";
import type { EmojiThrow as EmojiThrowType } from "@/lib/types";
import { EmojiPicker } from "./EmojiPicker";
import { EmojiThrow } from "./EmojiThrow";

interface EmojiCanvasProps {
	roomId?: string;
	enableWebSocket?: boolean;
}

export function EmojiCanvas({
	roomId = "global",
	enableWebSocket = true,
}: EmojiCanvasProps) {
	const [throws, setThrows] = useState<EmojiThrowType[]>([]);
	const { emojiEvents, throwEmoji, isConnected } = useRealtimeEmojis(roomId);

	// Convert WebSocket events to EmojiThrow format
	useEffect(() => {
		if (!enableWebSocket || emojiEvents.length === 0) return;

		const latestEvent = emojiEvents[emojiEvents.length - 1];

		const newThrow: EmojiThrowType = {
			id: latestEvent.id,
			emoji: latestEvent.emoji,
			from: latestEvent.from,
			to: "all",
			// Random position for WebSocket emojis
			x: Math.random() * (window.innerWidth - 100) + 50,
			y: Math.random() * (window.innerHeight - 100) + 50,
			timestamp: latestEvent.timestamp,
		};

		setThrows((prev) => [...prev, newThrow]);
	}, [emojiEvents, enableWebSocket]);

	const handleEmojiSelect = useCallback(
		(emoji: string) => {
			if (enableWebSocket && isConnected) {
				// Broadcast via WebSocket
				throwEmoji(emoji, "You");
			} else {
				// Local emoji throw (fallback)
				const newThrow: EmojiThrowType = {
					id: Math.random().toString(36).substring(7),
					emoji,
					from: "me",
					to: "all",
					x: window.innerWidth / 2,
					y: window.innerHeight - 100,
					timestamp: Date.now(),
				};

				setThrows((prev) => [...prev, newThrow]);
			}
		},
		[enableWebSocket, isConnected, throwEmoji],
	);

	const handleThrowComplete = useCallback((id: string) => {
		setThrows((prev) => prev.filter((t) => t.id !== id));
	}, []);

	return (
		<>
			{/* Emoji Throws */}
			{throws.map((throw_) => (
				<EmojiThrow
					key={throw_.id}
					throw={throw_}
					onComplete={handleThrowComplete}
				/>
			))}

			{/* Emoji Picker Button */}
			<div className="fixed bottom-8 left-1/2 z-40 -translate-x-1/2">
				<EmojiPicker onEmojiSelect={handleEmojiSelect} />
				{enableWebSocket && (
					<div className="mt-2 text-center text-xs">
						{isConnected ? (
							<span className="text-green-600 dark:text-green-400">
								🟢 WebSocket verbunden
							</span>
						) : (
							<span className="text-red-600 dark:text-red-400">
								🔴 WebSocket getrennt
							</span>
						)}
					</div>
				)}
			</div>
		</>
	);
}

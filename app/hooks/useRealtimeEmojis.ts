"use client";

import { useEffect, useState } from "react";
import { getEcho } from "@/lib/echo";
import type { Channel } from "laravel-echo";

export interface EmojiEvent {
	id: string;
	emoji: string;
	from: string;
	timestamp: number;
	room_id?: string;
}

const RANDOM_EMOJIS = [
	"👍",
	"👎",
	"❤️",
	"🎉",
	"😂",
	"😍",
	"🔥",
	"⭐",
	"💯",
	"🚀",
	"☕",
	"🍕",
	"🎯",
	"💪",
	"🙌",
	"👏",
	"🎊",
	"✨",
	"💥",
	"🌟",
];

export function useRealtimeEmojis(roomId = "global") {
	const [emojiEvents, setEmojiEvents] = useState<EmojiEvent[]>([]);
	const [channel, setChannel] = useState<Channel | null>(null);
	const [isConnected, setIsConnected] = useState(false);

	useEffect(() => {
		// Get Echo instance
		const echo = getEcho();

		// Subscribe to room channel
		const emojiChannel = echo.channel(`emoji-room.${roomId}`);

		// Listen for connection state
		emojiChannel.subscribed(() => {
			console.log(`Subscribed to emoji-room.${roomId}`);
			setIsConnected(true);
		});

		// Listen for emoji-throw events
		emojiChannel.listen(".emoji.thrown", (payload: EmojiEvent) => {
			console.log("Received emoji event:", payload);

			// Add random emoji if none specified
			const emoji = payload.emoji || getRandomEmoji();

			const newEvent: EmojiEvent = {
				id: payload.id || crypto.randomUUID(),
				emoji,
				from: payload.from || "Unknown",
				timestamp: payload.timestamp || Date.now(),
				room_id: roomId,
			};

			setEmojiEvents((prev) => [...prev, newEvent]);

			// Auto-remove after animation completes
			setTimeout(() => {
				setEmojiEvents((prev) => prev.filter((e) => e.id !== newEvent.id));
			}, 2500);
		});

		setChannel(emojiChannel);

		return () => {
			console.log(`Leaving channel emoji-room.${roomId}`);
			emojiChannel.stopListening(".emoji.thrown");
			echo.leave(`emoji-room.${roomId}`);
			setIsConnected(false);
		};
	}, [roomId]);

	// Function to broadcast emoji event
	const throwEmoji = (emoji?: string, from = "Anonymous") => {
		if (!channel) {
			console.warn("Channel not ready");
			return;
		}

		const event: EmojiEvent = {
			id: crypto.randomUUID(),
			emoji: emoji || getRandomEmoji(),
			from,
			timestamp: Date.now(),
			room_id: roomId,
		};

		// Trigger Laravel event via HTTP API
		// This will be sent to the backend which will broadcast it via Reverb
		fetch(`${process.env.NEXT_PUBLIC_API_URL}/api/emoji/throw`, {
			method: "POST",
			headers: {
				"Content-Type": "application/json",
			},
			body: JSON.stringify(event),
		}).catch((error) => {
			console.error("Failed to throw emoji:", error);
		});
	};

	return {
		emojiEvents,
		throwEmoji,
		isConnected,
	};
}

function getRandomEmoji(): string {
	return RANDOM_EMOJIS[Math.floor(Math.random() * RANDOM_EMOJIS.length)];
}

"use client";

import { motion } from "framer-motion";
import { useState } from "react";

const EMOJIS = [
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
];

interface EmojiPickerProps {
	onEmojiSelect: (emoji: string) => void;
}

export function EmojiPicker({ onEmojiSelect }: EmojiPickerProps) {
	const [isOpen, setIsOpen] = useState(false);

	return (
		<div className="relative">
			<motion.button
				type="button"
				onClick={() => setIsOpen(!isOpen)}
				whileHover={{ scale: 1.1 }}
				whileTap={{ scale: 0.95 }}
				className="rounded-full bg-gradient-to-r from-purple-500 to-pink-500 px-6 py-3 text-white shadow-lg hover:shadow-xl transition-shadow"
			>
				{isOpen ? "✖️ Schließen" : "🎯 Emoji werfen"}
			</motion.button>

			{isOpen && (
				<motion.div
					initial={{ opacity: 0, y: 10 }}
					animate={{ opacity: 1, y: 0 }}
					exit={{ opacity: 0, y: 10 }}
					className="absolute bottom-full mb-2 grid grid-cols-4 gap-2 rounded-lg bg-white p-4 shadow-xl border border-gray-200"
				>
					{EMOJIS.map((emoji) => (
						<motion.button
							type="button"
							key={emoji}
							onClick={() => {
								onEmojiSelect(emoji);
								setIsOpen(false);
							}}
							whileHover={{ scale: 1.2 }}
							whileTap={{ scale: 0.9 }}
							className="text-4xl hover:bg-gray-100 rounded-lg p-2 transition-colors"
						>
							{emoji}
						</motion.button>
					))}
				</motion.div>
			)}
		</div>
	);
}

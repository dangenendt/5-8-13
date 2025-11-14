"use client";

import { motion } from "framer-motion";
import { useEffect, useState } from "react";
import type { EmojiThrow as EmojiThrowType } from "@/lib/types";

interface EmojiThrowProps {
	throw: EmojiThrowType;
	onComplete: (id: string) => void;
}

export function EmojiThrow({ throw: emojiThrow, onComplete }: EmojiThrowProps) {
	const [windowSize, setWindowSize] = useState({ width: 0, height: 0 });

	useEffect(() => {
		setWindowSize({
			width: window.innerWidth,
			height: window.innerHeight,
		});
	}, []);

	// Random curve path for the emoji
	const randomCurve = Math.random() * 200 - 100;

	return (
		<motion.div
			initial={{
				x: emojiThrow.x,
				y: emojiThrow.y,
				scale: 1,
				opacity: 1,
			}}
			animate={{
				x: [
					emojiThrow.x,
					emojiThrow.x + randomCurve,
					Math.random() * windowSize.width,
				],
				y: [
					emojiThrow.y,
					emojiThrow.y - 200,
					Math.random() * windowSize.height,
				],
				scale: [1, 1.5, 0.5],
				opacity: [1, 1, 0],
				rotate: [0, 360, 720],
			}}
			transition={{
				duration: 2,
				ease: "easeInOut",
			}}
			onAnimationComplete={() => onComplete(emojiThrow.id)}
			className="pointer-events-none fixed z-50 text-6xl"
		>
			{emojiThrow.emoji}
		</motion.div>
	);
}

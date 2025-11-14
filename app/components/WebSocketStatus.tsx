"use client";

import { motion } from "framer-motion";

interface WebSocketStatusProps {
	isConnected: boolean;
	roomId?: string;
	showDetails?: boolean;
}

export function WebSocketStatus({
	isConnected,
	roomId,
	showDetails = true,
}: WebSocketStatusProps) {
	return (
		<motion.div
			initial={{ opacity: 0, y: -10 }}
			animate={{ opacity: 1, y: 0 }}
			className="inline-flex items-center gap-2 rounded-full bg-white/80 px-4 py-2 shadow-md backdrop-blur-sm dark:bg-gray-800/80"
		>
			<div
				className={`h-2 w-2 rounded-full ${
					isConnected ? "bg-green-500 animate-pulse" : "bg-red-500"
				}`}
			/>
			<span className="text-sm font-medium text-gray-700 dark:text-gray-300">
				{isConnected ? "Online" : "Offline"}
			</span>
			{showDetails && roomId && isConnected && (
				<span className="text-xs text-gray-500 dark:text-gray-400">
					({roomId})
				</span>
			)}
		</motion.div>
	);
}

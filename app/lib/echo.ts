import Echo from "laravel-echo";
import Pusher from "pusher-js";

// Make Pusher available globally for Laravel Echo
if (typeof window !== "undefined") {
	(window as any).Pusher = Pusher;
}

const echoConfig = {
	broadcaster: "reverb" as const,
	key: process.env.NEXT_PUBLIC_REVERB_APP_KEY || "poker-key",
	wsHost: process.env.NEXT_PUBLIC_REVERB_HOST || "localhost",
	wsPort: Number(process.env.NEXT_PUBLIC_REVERB_PORT) || 8080,
	wssPort: Number(process.env.NEXT_PUBLIC_REVERB_PORT) || 8080,
	forceTLS: (process.env.NEXT_PUBLIC_REVERB_SCHEME || "http") === "https",
	enabledTransports: ["ws", "wss"],
	disableStats: true,
};

let echoInstance: Echo<"reverb"> | null = null;

export function getEcho(): Echo<"reverb"> {
	if (!echoInstance && typeof window !== "undefined") {
		echoInstance = new Echo<"reverb">(echoConfig);
	}

	if (!echoInstance) {
		throw new Error("Echo instance could not be created");
	}

	return echoInstance;
}

export { echoInstance };

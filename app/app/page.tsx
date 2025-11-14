import { EmojiCanvas } from "@/components/EmojiCanvas";

export default function Home() {
	return (
		<div className="relative min-h-screen bg-gradient-to-br from-purple-50 via-pink-50 to-blue-50 dark:from-gray-900 dark:via-purple-900 dark:to-blue-900">
			{/* Emoji Canvas for throwing emojis */}
			<EmojiCanvas />

			{/* Main Content */}
			<main className="flex min-h-screen flex-col items-center justify-center px-4 py-16">
				<div className="mx-auto max-w-4xl text-center">
					{/* Header */}
					<div className="mb-12">
						<h1 className="mb-4 text-6xl font-bold bg-gradient-to-r from-purple-600 to-pink-600 bg-clip-text text-transparent">
							5-8-13 Planning Poker
						</h1>
						<p className="text-xl text-gray-600 dark:text-gray-300">
							Schätze Stories mit deinem Team in Echtzeit
						</p>
					</div>

					{/* Features */}
					<div className="mb-12 grid gap-6 md:grid-cols-3">
						<div className="rounded-2xl bg-white/80 p-6 shadow-lg backdrop-blur-sm dark:bg-gray-800/80">
							<div className="mb-4 text-5xl">🎯</div>
							<h3 className="mb-2 text-lg font-semibold text-gray-900 dark:text-white">
								Echtzeit Voting
							</h3>
							<p className="text-sm text-gray-600 dark:text-gray-300">
								Vote mit deinem Team in Echtzeit über Supabase
							</p>
						</div>

						<div className="rounded-2xl bg-white/80 p-6 shadow-lg backdrop-blur-sm dark:bg-gray-800/80">
							<div className="mb-4 text-5xl">🎉</div>
							<h3 className="mb-2 text-lg font-semibold text-gray-900 dark:text-white">
								Emoji Reactions
							</h3>
							<p className="text-sm text-gray-600 dark:text-gray-300">
								Wirf Emojis zu deinen Teammitgliedern
							</p>
						</div>

						<div className="rounded-2xl bg-white/80 p-6 shadow-lg backdrop-blur-sm dark:bg-gray-800/80">
							<div className="mb-4 text-5xl">📊</div>
							<h3 className="mb-2 text-lg font-semibold text-gray-900 dark:text-white">
								Story Tracking
							</h3>
							<p className="text-sm text-gray-600 dark:text-gray-300">
								Verwalte und schätze Stories mit verschiedenen Decks
							</p>
						</div>
					</div>

					{/* CTA */}
					<div className="flex flex-col items-center gap-4 sm:flex-row sm:justify-center">
						<a
							href="/rooms"
							className="rounded-full bg-gradient-to-r from-purple-600 to-pink-600 px-8 py-4 text-lg font-semibold text-white shadow-lg transition-transform hover:scale-105 hover:shadow-xl"
						>
							Room erstellen
						</a>
						<a
							href="/demo"
							className="rounded-full border-2 border-purple-600 px-8 py-4 text-lg font-semibold text-purple-600 transition-colors hover:bg-purple-50 dark:border-purple-400 dark:text-purple-400 dark:hover:bg-purple-900/20"
						>
							Demo ansehen
						</a>
					</div>

					{/* Tech Stack Info */}
					<div className="mt-16 rounded-2xl bg-white/80 p-8 shadow-lg backdrop-blur-sm dark:bg-gray-800/80">
						<h3 className="mb-6 text-2xl font-bold text-gray-900 dark:text-white">
							Tech Stack
						</h3>
						<div className="grid gap-4 md:grid-cols-2">
							<div className="text-left">
								<h4 className="mb-2 font-semibold text-purple-600 dark:text-purple-400">
									Frontend
								</h4>
								<ul className="space-y-1 text-sm text-gray-600 dark:text-gray-300">
									<li>✅ Next.js 16 mit App Router</li>
									<li>✅ TypeScript</li>
									<li>✅ Tailwind CSS v4</li>
									<li>✅ Framer Motion (Animationen)</li>
									<li>✅ Biome (Linting)</li>
								</ul>
							</div>
							<div className="text-left">
								<h4 className="mb-2 font-semibold text-pink-600 dark:text-pink-400">
									Backend
								</h4>
								<ul className="space-y-1 text-sm text-gray-600 dark:text-gray-300">
									<li>✅ Laravel 11</li>
									<li>✅ Supabase (PostgreSQL)</li>
									<li>✅ Realtime Subscriptions</li>
									<li>✅ Row Level Security</li>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</main>
		</div>
	);
}

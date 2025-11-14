# 5-8-13 Planning Poker - Frontend

Modern Planning Poker App mit Next.js, Supabase und Echtzeit-Features.

## Features

- 🎯 **Echtzeit Voting** - Vote mit deinem Team in Echtzeit über Supabase
- 🎉 **Emoji Reactions** - Wirf animierte Emojis zu deinen Teammitgliedern
- 📊 **Story Management** - Verwalte und schätze Stories mit verschiedenen Card Decks
- 🔄 **Live Updates** - Automatische Updates über Supabase Realtime
- 🎨 **Modern UI** - Schönes Design mit Tailwind CSS und Framer Motion

## Tech Stack

- **Next.js 16** mit App Router
- **TypeScript** für Type-Safety
- **Tailwind CSS v4** für Styling
- **Framer Motion** für Animationen
- **Supabase** für Datenbank und Realtime
- **Biome** für Linting und Formatting

## Setup

1. Installiere Dependencies:
```bash
npm install
```

2. Kopiere `.env.example` zu `.env.local` und füge deine Supabase Credentials ein:
```bash
cp .env.example .env.local
```

3. Starte den Development Server:
```bash
npm run dev
```

## Scripts

- `npm run dev` - Startet den Dev Server mit Turbopack
- `npm run build` - Erstellt Production Build
- `npm start` - Startet Production Server
- `npm run lint` - Führt Biome Linting aus
- `npm run lint:fix` - Führt Biome Linting aus und fixiert Fehler
- `npm run format` - Formatiert Code mit Biome

## Projektstruktur

```
app/
├── app/                    # Next.js App Router
│   ├── page.tsx           # Homepage
│   ├── demo/page.tsx      # WebSocket Demo Page
│   ├── layout.tsx         # Root Layout
│   └── globals.css        # Global Styles
├── components/            # React Components
│   ├── EmojiCanvas.tsx    # Emoji Throwing Container
│   ├── EmojiPicker.tsx    # Emoji Selection UI
│   ├── EmojiThrow.tsx     # Animated Emoji Component
│   └── WebSocketStatus.tsx # Connection Status Display
├── hooks/                 # Custom Hooks
│   └── useRealtimeEmojis.ts # WebSocket Event Handler
├── lib/                   # Utilities
│   ├── supabase.ts        # Supabase Client
│   └── types.ts           # TypeScript Types
└── public/                # Static Assets
```

## WebSocket Emoji Event Handler 🎉

Der Emoji-Werfer nutzt **Supabase Realtime** für Echtzeit-Events zwischen mehreren Clients!

### Features:
- ✅ **Broadcast Events** - Emojis werden an alle Clients im gleichen Room gesendet
- ✅ **Auto Random Emojis** - Zufällige Emojis wenn keine angegeben werden
- ✅ **Room-basiert** - Mehrere Rooms für verschiedene Sessions
- ✅ **In-Memory** - Keine Datenbank nötig, nur Broadcast

### Demo testen:

1. Starte den Dev-Server: `npm run dev`
2. Öffne `/demo` in deinem Browser
3. Öffne `/demo` in einem zweiten Tab
4. Wirf Emojis und sieh sie in beiden Tabs fliegen! ✨

**Tipp:** Öffne die Demo in mehreren Browser-Tabs mit der gleichen Room ID, um die Realtime-Synchronisation zu sehen!

📖 Siehe [WEBSOCKET_GUIDE.md](./WEBSOCKET_GUIDE.md) für detaillierte Anleitung

## Environment Variables

Erstelle eine `.env.local` Datei:

```env
NEXT_PUBLIC_SUPABASE_URL=your-supabase-url
NEXT_PUBLIC_SUPABASE_ANON_KEY=your-supabase-anon-key
```

## Card Decks

Unterstützte Card Decks:
- **Fibonacci**: 0, 1, 2, 3, 5, 8, 13, 21, 34, 55, 89, ?, ☕
- **Modified Fibonacci**: 0, ½, 1, 2, 3, 5, 8, 13, 20, 40, 100, ?, ☕
- **T-Shirt**: XS, S, M, L, XL, XXL, ?, ☕
- **Powers of 2**: 0, 1, 2, 4, 8, 16, 32, 64, ?, ☕

## License

MIT

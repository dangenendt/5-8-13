export interface Room {
	id: string;
	name: string;
	slug: string;
	card_deck: "fibonacci" | "modified_fibonacci" | "tshirt" | "powers_of_2";
	allow_observers: boolean;
	voting_time_limit: number | null;
	created_at: string;
	updated_at: string;
}

export interface RoomParticipant {
	id: number;
	room_id: string;
	name: string;
	role: "admin" | "participant" | "observer";
	is_online: boolean;
	last_seen_at: string | null;
	created_at: string;
	updated_at: string;
}

export interface Story {
	id: number;
	room_id: string;
	title: string;
	description: string | null;
	"3rd_party_ident": string | null;
	"3rd_party_url": string | null;
	final_estimate: string | null;
	sort_order: number;
	voting_started_at: string | null;
	revealed_at: string | null;
	created_at: string;
	updated_at: string;
}

export interface Vote {
	id: number;
	room_id: string;
	participant_id: number;
	story_id: number;
	vote: string;
	created_at: string;
	updated_at: string;
}

export interface EmojiThrow {
	id: string;
	emoji: string;
	from: string;
	to: string;
	x: number;
	y: number;
	timestamp: number;
}

<?php

namespace Database\Seeders;

use App\Models\Room;
use App\Models\RoomParticipant;
use App\Models\Story;
use App\Models\Vote;
use Illuminate\Database\Seeder;

class RoomSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Room 1: Sprint Planning Demo (Fibonacci)
        $room1 = Room::create([
            'name' => 'Sprint Planning Demo',
            'card_deck' => 'fibonacci',
            'allow_observers' => true,
            'voting_time_limit' => null,
        ]);

        // Participants for Room 1
        $alice = RoomParticipant::create([
            'room_id' => $room1->id,
            'name' => 'Alice Johnson',
            'role' => 'admin',
            'is_online' => true,
            'last_seen_at' => now(),
        ]);

        $bob = RoomParticipant::create([
            'room_id' => $room1->id,
            'name' => 'Bob Smith',
            'role' => 'participant',
            'is_online' => true,
            'last_seen_at' => now(),
        ]);

        $charlie = RoomParticipant::create([
            'room_id' => $room1->id,
            'name' => 'Charlie Davis',
            'role' => 'participant',
            'is_online' => true,
            'last_seen_at' => now()->subMinutes(5),
        ]);

        $observer = RoomParticipant::create([
            'room_id' => $room1->id,
            'name' => 'Diana Observer',
            'role' => 'observer',
            'is_online' => true,
            'last_seen_at' => now(),
        ]);

        // Stories for Room 1
        // Story 1: Completed story with votes and final estimate
        $story1 = Story::create([
            'room_id' => $room1->id,
            'title' => 'Implement User Authentication',
            'description' => 'Add JWT-based authentication with email/password login',
            '3rd_party_ident' => 'PROJ-101',
            '3rd_party_url' => 'https://jira.example.com/browse/PROJ-101',
            'final_estimate' => '8',
            'sort_order' => 0,
            'voting_started_at' => now()->subHours(2),
            'revealed_at' => now()->subHours(1)->subMinutes(30),
        ]);

        Vote::create(['room_id' => $room1->id, 'participant_id' => $alice->id, 'story_id' => $story1->id, 'vote' => '8']);
        Vote::create(['room_id' => $room1->id, 'participant_id' => $bob->id, 'story_id' => $story1->id, 'vote' => '8']);
        Vote::create(['room_id' => $room1->id, 'participant_id' => $charlie->id, 'story_id' => $story1->id, 'vote' => '5']);

        // Story 2: Currently voting (votes hidden)
        $story2 = Story::create([
            'room_id' => $room1->id,
            'title' => 'WebSocket Real-time Updates',
            'description' => 'Implement real-time voting updates using WebSockets',
            '3rd_party_ident' => 'PROJ-102',
            '3rd_party_url' => 'https://jira.example.com/browse/PROJ-102',
            'sort_order' => 1,
            'voting_started_at' => now()->subMinutes(10),
        ]);

        Vote::create(['room_id' => $room1->id, 'participant_id' => $alice->id, 'story_id' => $story2->id, 'vote' => '13']);
        Vote::create(['room_id' => $room1->id, 'participant_id' => $bob->id, 'story_id' => $story2->id, 'vote' => '8']);

        // Story 3: Revealed story with diverse votes
        $story3 = Story::create([
            'room_id' => $room1->id,
            'title' => 'Jira API Integration',
            'description' => 'Connect to Jira API to import user stories automatically',
            '3rd_party_ident' => 'PROJ-103',
            '3rd_party_url' => 'https://jira.example.com/browse/PROJ-103',
            'sort_order' => 2,
            'voting_started_at' => now()->subHours(1),
            'revealed_at' => now()->subMinutes(30),
        ]);

        Vote::create(['room_id' => $room1->id, 'participant_id' => $alice->id, 'story_id' => $story3->id, 'vote' => '13']);
        Vote::create(['room_id' => $room1->id, 'participant_id' => $bob->id, 'story_id' => $story3->id, 'vote' => '21']);
        Vote::create(['room_id' => $room1->id, 'participant_id' => $charlie->id, 'story_id' => $story3->id, 'vote' => '?']);

        // Story 4: Pending story
        Story::create([
            'room_id' => $room1->id,
            'title' => 'Email Notification System',
            'description' => 'Send email notifications when voting is complete',
            '3rd_party_ident' => 'PROJ-104',
            '3rd_party_url' => 'https://jira.example.com/browse/PROJ-104',
            'sort_order' => 3,
        ]);

        // Story 5: Another pending story
        Story::create([
            'room_id' => $room1->id,
            'title' => 'Admin Dashboard',
            'description' => 'Create admin dashboard for room management',
            'sort_order' => 4,
        ]);

        // Room 2: T-Shirt Sizing (with time limit)
        $room2 = Room::create([
            'name' => 'Product Backlog Refinement',
            'card_deck' => 'tshirt',
            'allow_observers' => false,
            'voting_time_limit' => 300, // 5 minutes
        ]);

        // Participants for Room 2
        $eve = RoomParticipant::create([
            'room_id' => $room2->id,
            'name' => 'Eve Martinez',
            'role' => 'admin',
            'is_online' => true,
            'last_seen_at' => now(),
        ]);

        $frank = RoomParticipant::create([
            'room_id' => $room2->id,
            'name' => 'Frank Wilson',
            'role' => 'participant',
            'is_online' => false,
            'last_seen_at' => now()->subHours(1),
        ]);

        $grace = RoomParticipant::create([
            'room_id' => $room2->id,
            'name' => 'Grace Lee',
            'role' => 'participant',
            'is_online' => true,
            'last_seen_at' => now(),
        ]);

        // Stories for Room 2
        $story4 = Story::create([
            'room_id' => $room2->id,
            'title' => 'Mobile App MVP',
            'description' => 'Build minimum viable product for mobile app',
            'final_estimate' => 'XL',
            'sort_order' => 0,
            'voting_started_at' => now()->subDays(1),
            'revealed_at' => now()->subDays(1)->addHours(1),
        ]);

        Vote::create(['room_id' => $room2->id, 'participant_id' => $eve->id, 'story_id' => $story4->id, 'vote' => 'L']);
        Vote::create(['room_id' => $room2->id, 'participant_id' => $grace->id, 'story_id' => $story4->id, 'vote' => 'XL']);

        Story::create([
            'room_id' => $room2->id,
            'title' => 'Dark Mode Theme',
            'description' => 'Add dark mode support to the application',
            'sort_order' => 1,
        ]);

        // Room 3: Modified Fibonacci (no participants yet - new room)
        $room3 = Room::create([
            'name' => 'Team Alpha Sprint 23',
            'card_deck' => 'modified_fibonacci',
            'allow_observers' => true,
            'voting_time_limit' => 180, // 3 minutes
        ]);

        Story::create([
            'room_id' => $room3->id,
            'title' => 'Database Migration',
            'description' => 'Migrate from MySQL to PostgreSQL',
            'sort_order' => 0,
        ]);

        Story::create([
            'room_id' => $room3->id,
            'title' => 'API Rate Limiting',
            'description' => 'Implement rate limiting for public API endpoints',
            'sort_order' => 1,
        ]);

        // Room 4: Powers of 2 (completed session)
        $room4 = Room::create([
            'name' => 'Bug Bash Estimation',
            'card_deck' => 'powers_of_2',
            'allow_observers' => true,
            'voting_time_limit' => null,
        ]);

        $henry = RoomParticipant::create([
            'room_id' => $room4->id,
            'name' => 'Henry Brown',
            'role' => 'admin',
            'is_online' => false,
            'last_seen_at' => now()->subDays(2),
        ]);

        $iris = RoomParticipant::create([
            'room_id' => $room4->id,
            'name' => 'Iris Chen',
            'role' => 'participant',
            'is_online' => false,
            'last_seen_at' => now()->subDays(2),
        ]);

        $story5 = Story::create([
            'room_id' => $room4->id,
            'title' => 'Fix Login Bug on Safari',
            'description' => 'Users cannot login on Safari browser',
            'final_estimate' => '4',
            'sort_order' => 0,
            'voting_started_at' => now()->subDays(2),
            'revealed_at' => now()->subDays(2)->addMinutes(5),
        ]);

        Vote::create(['room_id' => $room4->id, 'participant_id' => $henry->id, 'story_id' => $story5->id, 'vote' => '4']);
        Vote::create(['room_id' => $room4->id, 'participant_id' => $iris->id, 'story_id' => $story5->id, 'vote' => '4']);

        $story6 = Story::create([
            'room_id' => $room4->id,
            'title' => 'Performance Optimization',
            'description' => 'Optimize database queries for dashboard',
            'final_estimate' => 'skipped',
            'sort_order' => 1,
            'voting_started_at' => now()->subDays(2)->addMinutes(10),
            'revealed_at' => now()->subDays(2)->addMinutes(10),
        ]);
    }
}

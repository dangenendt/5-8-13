<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('ALTER PUBLICATION supabase_realtime ADD TABLE rooms;');
        DB::statement('ALTER PUBLICATION supabase_realtime ADD TABLE room_participants;');
        DB::statement('ALTER PUBLICATION supabase_realtime ADD TABLE stories;');
        DB::statement('ALTER PUBLICATION supabase_realtime ADD TABLE votes;');

        // Enable Row Level Security
        DB::statement('ALTER TABLE rooms ENABLE ROW LEVEL SECURITY');
        DB::statement('ALTER TABLE room_participants ENABLE ROW LEVEL SECURITY');
        DB::statement('ALTER TABLE stories ENABLE ROW LEVEL SECURITY');
        DB::statement('ALTER TABLE votes ENABLE ROW LEVEL SECURITY');

        // ==========================================
        // ROOMS: Jeder kann lesen, niemand kann direkt schreiben (nur via API)
        // ==========================================
        DB::statement('
            CREATE POLICY "Anyone can view rooms"
                ON rooms FOR SELECT
                USING (true)
        ');

        // Optional: Rooms können nur via API erstellt werden (Laravel Backend)
        DB::statement('
            CREATE POLICY "Service role can insert rooms"
                ON rooms FOR INSERT
                WITH CHECK (false)
        ');

        DB::statement('
            CREATE POLICY "Service role can update rooms"
                ON rooms FOR UPDATE
                USING (false)
        ');

        // ==========================================
        // ROOM_PARTICIPANTS: Jeder kann lesen
        // ==========================================
        DB::statement('
            CREATE POLICY "Anyone can view participants"
                ON room_participants FOR SELECT
                USING (true)
        ');

        DB::statement('
            CREATE POLICY "Service role can manage participants"
                ON room_participants FOR ALL
                USING (false)
        ');

        // ==========================================
        // STORIES: Jeder kann lesen
        // ==========================================
        DB::statement('
            CREATE POLICY "Anyone can view stories"
                ON stories FOR SELECT
                USING (true)
        ');

        DB::statement('
            CREATE POLICY "Service role can manage stories"
                ON stories FOR ALL
                USING (false)
        ');

        // ==========================================
        // VOTES: Nur sichtbar wenn revealed
        // ==========================================
        DB::statement('
            CREATE POLICY "Anyone can view votes when revealed"
                ON votes FOR SELECT
                USING (
                    EXISTS (
                        SELECT 1 FROM stories
                        WHERE stories.id = votes.story_id
                        AND stories.revealed_at IS NOT NULL
                    )
                )
        ');

        // Admins können immer alle Votes sehen (optional)
        DB::statement('
            CREATE POLICY "Admins can view all votes"
                ON votes FOR SELECT
                USING (
                    EXISTS (
                        SELECT 1 FROM room_participants
                        WHERE room_participants.id = votes.participant_id
                        AND room_participants.role = \'admin\'
                    )
                )
        ');

        DB::statement('
            CREATE POLICY "Service role can manage votes"
                ON votes FOR ALL
                USING (false)
        ');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop all policies
        DB::statement('DROP POLICY IF EXISTS "Anyone can view rooms" ON rooms');
        DB::statement('DROP POLICY IF EXISTS "Service role can insert rooms" ON rooms');
        DB::statement('DROP POLICY IF EXISTS "Service role can update rooms" ON rooms');

        DB::statement('DROP POLICY IF EXISTS "Anyone can view participants" ON room_participants');
        DB::statement('DROP POLICY IF EXISTS "Service role can manage participants" ON room_participants');

        DB::statement('DROP POLICY IF EXISTS "Anyone can view stories" ON stories');
        DB::statement('DROP POLICY IF EXISTS "Service role can manage stories" ON stories');

        DB::statement('DROP POLICY IF EXISTS "Anyone can view votes when revealed" ON votes');
        DB::statement('DROP POLICY IF EXISTS "Admins can view all votes" ON votes');
        DB::statement('DROP POLICY IF EXISTS "Service role can manage votes" ON votes');

        // Disable Row Level Security
        DB::statement('ALTER TABLE rooms DISABLE ROW LEVEL SECURITY');
        DB::statement('ALTER TABLE room_participants DISABLE ROW LEVEL SECURITY');
        DB::statement('ALTER TABLE stories DISABLE ROW LEVEL SECURITY');
        DB::statement('ALTER TABLE votes DISABLE ROW LEVEL SECURITY');
    }
};

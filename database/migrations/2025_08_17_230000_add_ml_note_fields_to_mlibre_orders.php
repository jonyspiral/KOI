<?php
// database/migrations/2025_08_17_230000_add_ml_note_fields_to_mlibre_orders.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('mlibre_orders', function (Blueprint $t) {
            if (!Schema::hasColumn('mlibre_orders','ml_note_id')) {
                $t->string('ml_note_id', 64)->nullable()->index();
            }
            if (!Schema::hasColumn('mlibre_orders','ml_note_posted_at')) {
                $t->timestamp('ml_note_posted_at')->nullable();
            }
            if (!Schema::hasColumn('mlibre_orders','ml_note_text')) {
                $t->text('ml_note_text')->nullable();
            }
        });
    }
    public function down(): void {
        Schema::table('mlibre_orders', function (Blueprint $t) {
            if (Schema::hasColumn('mlibre_orders','ml_note_id')) $t->dropColumn('ml_note_id');
            if (Schema::hasColumn('mlibre_orders','ml_note_posted_at')) $t->dropColumn('ml_note_posted_at');
            if (Schema::hasColumn('mlibre_orders','ml_note_text')) $t->dropColumn('ml_note_text');
        });
    }
};

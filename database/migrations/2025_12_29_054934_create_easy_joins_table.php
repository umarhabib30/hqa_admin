    <?php

    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Support\Facades\Schema;

    return new class extends Migration
    {
        /**
         * Run the migrations.
         */
        public function up(): void
        {
            Schema::create('easy_joins', function (Blueprint $table) {
                $table->id();
                $table->foreignId('fee_person_price_id')->constrained()->cascadeOnDelete();
                $table->string('first_name');
                $table->string('last_name');
                $table->string('email')->unique();

                $table->enum('is_attending', ['yes', 'no']);

                $table->unsignedInteger('guest_count')->default(1);
                $table->decimal('fee_per_person', 10, 2);
                $table->decimal('total_fee', 10, 2);
                $table->timestamps();
            });
        }

        /**
         * Reverse the migrations.
         */
        public function down(): void
        {
            Schema::dropIfExists('easy_joins');
        }
    };

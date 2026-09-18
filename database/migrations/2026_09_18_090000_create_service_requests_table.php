<?php

use App\Models\ServiceRequest;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('service_requests', function (Blueprint $table): void {
            $table->id();
            $table->string('requester_name', 150);
            $table->string('requester_email');
            $table->string('request_type', 50)->index();
            $table->text('description');
            $table->string('status', 30)
                ->default(ServiceRequest::STATUS_NEW)
                ->index();
            $table->timestamps();

            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('service_requests');
    }
};

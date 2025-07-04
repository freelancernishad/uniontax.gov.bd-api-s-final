<?php

namespace App\Jobs;

use App\Models\Holdingtax;
use App\Models\JobStatusLog;
use App\Models\HoldingBokeya;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class RenewHoldingTaxJob implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    protected $union;

    public function __construct($union)
    {
        $this->union = $union;
    }

    public function handle()
    {
        try {
            $currentOrthoBochor = CurrentOrthoBochor(1);
            $previousOrthoBochor = PreviousOrthoBochor(1);

            $holdings = Holdingtax::select('id', 'unioun', 'holding_no')
                ->where('unioun', $this->union)
                ->get();

            $createdCount = 0;

            foreach ($holdings as $holding) {
                $hasCurrent = $holding->holdingBokeyas()
                    ->where('year', $currentOrthoBochor)
                    ->where('price', '>', 0)
                    ->exists();

                if ($hasCurrent) continue;

                $previousBokeyas = $holding->holdingBokeyas()
                    ->where('year', $previousOrthoBochor)
                    ->where('price', '>', 0)
                    ->get();

                if ($previousBokeyas->isEmpty()) continue;

                $source = $previousBokeyas->count() === 1
                    ? $previousBokeyas->first()
                    : ($previousBokeyas->where('status', 'Paid')->first()
                        ?? $previousBokeyas->sortByDesc('id')->first());

                if ($source) {
                    HoldingBokeya::create([
                        'holdingTax_id' => $holding->id,
                        'year' => $currentOrthoBochor,
                        'price' => $source->price,
                        'payYear' => null,
                        'payOB' => null,
                        'status' => 'Unpaid',
                    ]);
                    $createdCount++;
                }
            }

            // ✅ Success log
            JobStatusLog::create([
                'job_name' => 'RenewHoldingTaxJob',
                'status' => 'success',
                'message' => "{$createdCount} bokeya created for union {$this->union}"
            ]);

        } catch (\Exception $e) {
            // এইখানে না রাখলেও হবে, কারণ নিচে `failed()` আছে
            throw $e;
        }
    }

    // 🔴 ব্যর্থ হলে এই মেথড অটো কল হয়
    public function failed(\Throwable $exception)
    {
        $message = "Union: {$this->union} | Error: " . $exception->getMessage();

        // ✅ Database Log
        JobStatusLog::create([
            'job_name' => 'RenewHoldingTaxJob',
            'status' => 'failed',
            'message' => $message . "\n\nTrace:\n" . $exception->getTraceAsString()
        ]);

        // ✅ Laravel storage log এও লিখুন
        Log::error('[RenewHoldingTaxJob Failed] ' . $message, [
            'trace' => $exception->getTrace()
        ]);
    }
}

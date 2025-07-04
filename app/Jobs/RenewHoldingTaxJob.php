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
use Illuminate\Foundation\Bus\Dispatchable;

class RenewHoldingTaxJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Set a practically unlimited timeout (in seconds).
     * Laravel 10+ supports $timeout = 0 as unlimited.
     */
    public $timeout = 999999;

    protected $union;

    /**
     * Create a new job instance.
     */
    public function __construct($union)
    {
        $this->union = $union;
    }

    /**
     * Execute the job.
     */
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

            // ✅ Success log to DB
            JobStatusLog::create([
                'job_name' => 'RenewHoldingTaxJob',
                'status' => 'success',
                'message' => "{$createdCount} bokeya created for union {$this->union}",
            ]);

            // ✅ Laravel log
            Log::info("RenewHoldingTaxJob succeeded for union: {$this->union} with {$createdCount} new bokeya");

        } catch (\Exception $e) {
            // fallback in case failed() doesn't fire
            $this->logFailure($e);
            throw $e;
        }
    }

    /**
     * Handle job failure.
     */
    public function failed(\Throwable $exception)
    {
        $this->logFailure($exception);
    }

    /**
     * Common method to log failures.
     */
    private function logFailure(\Throwable $exception)
    {
        $message = "Union: {$this->union} | Error: " . $exception->getMessage();

        // ✅ Database log
        JobStatusLog::create([
            'job_name' => 'RenewHoldingTaxJob',
            'status' => 'failed',
            'message' => $message . "\n\nTrace:\n" . $exception->getTraceAsString(),
        ]);

        // ✅ Laravel error log
        Log::error('[RenewHoldingTaxJob Failed] ' . $message, [
            'trace' => $exception->getTrace(),
        ]);
    }
}

<?php

namespace App\Jobs;

use App\Models\QueueLogs;
use Illuminate\Bus\Queueable;
use Illuminate\Bus\Batchable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Bus\Batch;
use Illuminate\Support\Facades\Bus;

use Http;

class SendRequest implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $url;
    protected $highPriorityBatchId;
    protected $city;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($url, $highPriorityBatchId)
    {
        $this->url = $url;
        $this->highPriorityBatchId = $highPriorityBatchId;
        $this->city = $city;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $maximumPendingJobsOnQueue = 100000;
        $timeout = 0;  // Maximum number of seconds to wait for a response in seconds.
        $retries = 0;  // Maximum number of times the request should be attempted,
        $waiting = 100; // and the number of milliseconds that should wait in between attempts.
        $response = Http::timeout($timeout)
                    ->retry($retries, $waiting)
                    ->withToken('Sk2A9GyUgd8kyGdM85fN5gmAKnsB')
                    ->post($this->url, [
                        'city' => $this->city,
                    ]);
        $log = QueueLogs::insert([
            'batch_id'  => $this->batch()->id,
            'log'       => 'Start job sending post to '.$this->url,
            'help'      => ''
        ]);
        // Evaluating possible response scenarios
        if ($response->successful()){
            $log = QueueLogs::insert([
                'batch_id'  => $this->batch()->id,
                'log'       => 'Request processed correctly. Status:  '.$response->status(),
                'help'      => 'The job has been released from the queue.'
            ]);  
            // ----------------------------------------------------------------------------------------------------------------------
            // At the time of processing the work, the progress rate of the work is evaluated, contemplating a limit to implement here 
            // an automated infrastructure solution through APIs such as DigitalOcean, Amazon or Google Engine.
            // In this case, for simplicity, we compare pending jobs with the maximumPendingJobsOnQueue variable.
            // ---------------------------------------------------------------------------------------------------------------------- 
            if ($this->batch()->pendingJobs() >= $maximumPendingJobsOnQueue){
                // Implement something to increment the infrastructure here....
            }
        }
        // If it fails for any reason, the job will be re-queuing until it is processed correctly. 
        // In this regard, it is important to consider re-gluing it with higher priority to be processed before the subsequent jobs.
        if ($response->failed()){
            $log = QueueLogs::insert([
                'batch_id'  => $this->batch()->id,
                'log'       => 'Request failed. Status:  '.$response->status(),
                'help'      => 'Re-queue the process in the High Priority Queue until this has been processed correctly.'
            ]);  
            // ----------------------------------------------------------------------------------------------------------------------
            // The request is re-queued in the 'High' priority queue until it is processed correctly
            // You could be considering an maximum Pending Jobs to prevend a crash server, 
            // on this case the maximum pending jobs on the queue are 100k requests
            // ----------------------------------------------------------------------------------------------------------------------
            if ($this->batch()->pendingJobs <= $maximumPendingJobsOnQueue){
                $batch = Bus::findBatch($this->highPriorityBatchId);
                $batch->add(new SendRequest($this->url,$this->highPriorityBatchId));
            }
            // Failing manually the process so that the job goes to the failed job queue             
            $this->fail();
            return false;
        }
        if ($response->clientError()){
            $log = QueueLogs::insert([
                'batch_id'  => $this->batch()->id,
                'log'       => 'Request failed. Status:  '.$response->status(),
                'help'      => 'Make sure you meet all the requirements of the queryes.'
            ]); 
        }
        if ($response->serverError()){
            $log = QueueLogs::insert([
                'batch_id'  => $this->batch()->id,
                'log'       => 'The server is down or could not process the request. Status:  '.$response->status(),
                'help'      => 'Check the server for possible service failures.'
            ]);  
        }                       
        return true;
    }

    public function failed(Throwable $exception)
    {
        // Nothing to do here...
    }
}

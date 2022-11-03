<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Bus\Batch;
use Illuminate\Bus\BatchRepository;
use Illuminate\Support\Facades\Bus;
use App\Jobs\SendPost;
use App\Models\QueueLogs;

class HomeController extends Controller
{
    public $batches;

    public function __construct(BatchRepository $batches){
        $this->batches = $batches;
    }

    public function index(Request $request){
        $logs = QueueLogs::all()->sortByDesc("id");
        $batches = $this->batches->get();
        return view('home')
                ->with('batches', $batches)
                ->with('logs', $logs);
    }
}
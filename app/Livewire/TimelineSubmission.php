<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Submission;
use App\Enums\StatusSubmissionEnum;

class TimelineSubmission extends Component
{
    public $record;
    public $status = ''; // Default tahap awal
    public bool $media = false;
    public bool $pending = false;
    public bool $diterima = false;
    public bool $ditolak = false;

    public function mount(Submission | null $record)
    {
        if ($record) {
            $this->record = $record;

            // Cek apakah ada media
            $media = $this->record->getFirstMedia('file_submissions');
            $this->media = $media !== null;

            // Set status dari record
            $this->status = $record->status;

            // Set status boolean berdasarkan enum
            // dd($this->status);
            $this->pending = $this->status === StatusSubmissionEnum::PENDING;
            $this->diterima = $this->status === StatusSubmissionEnum::APPROVED;
            $this->ditolak = $this->status === StatusSubmissionEnum::REJECTED;
        }
    }

    public function render()
    {
        return view('livewire.timeline-submission');
    }
}

<?php declare(strict_types=1) ?>
<h1>{{ config('mail.ai-model-created.subject') }}</h1>
<hr/>
<div>
    <b>Name:</b> {{ $name }}
</div>
<div>
    <b>Email:</b> {{ $email }}
</div>
<div>
    <b>User ID:</b> {{ $userData->user_id }}
</div>
<div>
    <b>Registration date:</b> {{ $userData->created_at->format('d.m.Y H:i:s') }}
</div>
<hr/>
<br>
<div>
    <h4>{{ $msg }}</h4>
</div>
<br>
@php
    $AIModels = \App\Models\AIModel::query()
                              ->where('user_id', $userData->user_id)
                              ->where(fn($q) =>
                                  $q->where('status', \App\Enums\AITrainingStatus::Created)
                                      ->orWhere('status', \App\Enums\AITrainingStatus::Queuing)
                                      ->orWhere('status', \App\Enums\AITrainingStatus::Training)
                                      ->orWhere('status', \App\Enums\AITrainingStatus::Deploying))
                              ->get()
@endphp
@if($AIModels->count())
<div>
    <table border="1" cellspacing="0" cellpadding="5" style="width: 100%; border-collapse: collapse;">
        <thead>
        <tr>
            <th>Name</th>
            <th>Status</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td>{{ $AIModel->name }}</td>
            <td>{{ $AIModel->status }}</td>
        </tr>
        @foreach($AIModels as $AIModel)
            <tr>
                <td>{{ $AIModel->name }}</td>
                <td>{{ $AIModel->status }}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
@endif

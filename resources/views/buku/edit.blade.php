@extends('layouts.app')

@section('title', 'Edit Buku')

@section('content')
<div class="card">
    <div class="card-header bg-warning">
        <h4 class="mb-0"><i class="bi bi-pencil"></i> Edit Buku</h4>
    </div>
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Data belum valid:</strong>
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('buku.update', $buku->id) }}" method="POST">
            @csrf
            @method('PUT')
            @include('buku.form', ['buku' => $buku])
        </form>
    </div>
</div>
@endsection

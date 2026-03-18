@extends('layouts.app')

@section('content')

<h1>Articles</h1>

<form action="/articles" method="GET" class="mb-4">
    <div class="row g-2">
        <div class="col-md-5">
            <input type="text" name="search" class="form-control" placeholder="Search keywords..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <select name="level" class="form-select">
                <option value="">All Levels</option>
                @if(isset($levels))
                    @foreach($levels as $level)
                        <option value="{{ $level }}" {{ request('level') == $level ? 'selected' : '' }}>{{ ucfirst($level) }}</option>
                    @endforeach
                @endif
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-primary w-100">Filter</button>
        </div>
        <div class="col-md-2">
            <a href="/articles" class="btn btn-secondary w-100">Reset</a>
        </div>
    </div>
</form>

@foreach($articles as $article)

<div class="card mb-3">
<div class="card-body">

<h3>{{ $article->title }}</h3>

<p>
Author: {{ $article->author }} |
Level: {{ $article->level }}
</p>

<a href="/articles/{{ $article->id }}" class="btn btn-primary btn-sm">
Read
</a>

</div>
</div>

@endforeach

<div class="d-flex justify-content-center mt-4">
    {{ $articles->links('pagination::bootstrap-5') }}
</div>

@endsection
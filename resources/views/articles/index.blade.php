@extends('layouts.app')

@section('content')

<h1>Articles</h1>

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

@endsection
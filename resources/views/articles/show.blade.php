@extends('layouts.app')

@section('content')

<h1>{{ $article->title }}</h1>

<p>
Author: {{ $article->author }} |
Level: {{ $article->level }}
</p>

<hr>

<p>{{ $article->content }}</p>

<a href="/articles" class="btn btn-secondary mt-3">
Back
</a>

@endsection
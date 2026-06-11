@extends('layouts.site')

@section('title', $location->name)

@section('content')
<section class="site-hero" style="min-height:280px">
    <div class="container position-relative py-5 text-center">
        <h1 class="site-serif display-5 fw-bold">{{ $location->name }}</h1>
        <p class="lead mb-0 opacity-90">{{ $location->city }}{{ $location->province ? ', '.$location->province : '' }} · {{ $location->country }}</p>
        @if($location->description)<p class="mt-3 mb-0 opacity-75 col-lg-8 mx-auto">{{ $location->description }}</p>@endif
    </div>
</section>

<div class="container py-5">
    <h2 class="h5 fw-bold mb-4">{{ $properties->total() }} stays in {{ $location->name }}</h2>
    <div class="row g-4">
        @forelse($properties as $property)
            <div class="col-md-6 col-lg-4">@include('site.partials.property-card', ['property' => $property])</div>
        @empty
            <div class="col-12 text-center text-muted py-5">No active properties in this destination yet.</div>
        @endforelse
    </div>
    <div class="mt-4">{{ $properties->links() }}</div>
</div>
@endsection

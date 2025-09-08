@extends('layouts.dashboard')

@section('title', 'eCommerce Dashboard')

@section('content')
  <div class="p-4 mx-auto max-w-screen-2xl md:p-6 2xl:p-10">
    <div class="grid grid-cols-12 gap-4 md:gap-6 2xl:gap-7.5">
      <div class="col-span-12 space-y-6 xl:col-span-7">
        @include('partials.metric-group.metric-group-01')
        @include('partials.chart.chart-01')
        </div>
      <div class="col-span-12 xl:col-span-5">
        @include('partials.chart.chart-02')
        </div>

      </div>
  </div>
@endsection
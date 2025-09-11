@extends('layouts.dashboard')

@section('title', 'Karyawan')

@section('content')
<div class="p-4 mx-auto max-w-screen-2xl md:p-6 2xl:p-10">
  @include('partials.table.table-01')
</div>
@endsection

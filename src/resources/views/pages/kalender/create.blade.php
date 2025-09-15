@extends('layouts.dashboard')

@section('content')
<div class="container mx-auto">
    <h1 class="text-2xl font-bold mb-4">Tambah Event Kalender</h1>

    {{-- Tombol untuk membuka modal --}}
    <button id="btn-add-event" class="btn btn-primary mb-4">Tambah Event</button>

    {{-- Include modal --}}
    @include('partials.calendar-event-modal')
</div>

{{-- JQuery --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    // Fungsi open/close modal
    function openModal() {
        $('#eventModal').removeClass('hidden');
    }
    function closeModal() {
        $('#eventModal').addClass('hidden');
        $('#form-tambah-event')[0].reset();
    }

    // Event klik tombol
    $('#btn-add-event').click(openModal);
    $('.modal-close-btn').click(closeModal);

    // Submit form via AJAX
    $('#form-tambah-event').submit(function(e){
        e.preventDefault();

        $.ajax({
            url: "{{ route('kalender.store') }}",
            type: "POST",
            data: $(this).serialize(),
            success: function(res) {
                alert('Event berhasil ditambahkan!');
                closeModal();
            },
            error: function(err){
                alert('Terjadi error. Periksa inputan!');
            }
        });
    });
});
</script>
@endsection

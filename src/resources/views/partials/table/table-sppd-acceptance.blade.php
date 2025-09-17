<tr>
    <td class="border px-4 py-2">{{ $loop->iteration }}</td>
    <td class="border px-4 py-2">{{ $item->nama }}</td>
    <td class="border px-4 py-2">{{ $item->tanggal }}</td>
    <td class="border px-4 py-2">{{ $item->tujuan }}</td>
    <td class="border px-4 py-2 text-center">
        @if($item->status == 'accept')
            ✅ Diterima
        @elseif($item->status == 'reject')
            ❌ Ditolak
        @else
            ⏳ Menunggu
        @endif
    </td>
    <td class="border px-4 py-2 text-center">
        @if($item->status == 'pending')
            <form action="{{ route('sppd.updateStatus', $item->id) }}" method="POST">
            @csrf
            @method('PATCH')
                <button name="status" value="accept"
                    class="bg-green-500 hover:bg-green-600 text-white px-2 py-1 rounded">
                    Accept
                </button>
                <button name="status" value="reject"
                    class="bg-red-500 hover:bg-red-600 text-white px-2 py-1 rounded">
                    Tolak
                </button>
            </form>
        @else
            <span class="text-gray-400 italic">Sudah diproses</span>
        @endif
    </td>
</tr>
